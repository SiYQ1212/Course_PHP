<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    die(json_encode(['success' => false, 'message' => '无权限']));
}

require_once '../../dbConfig.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('无效的数据格式');
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 如果要更新邮箱，先检查是否重复
    if (isset($data['email']) && !empty($data['email'])) {
        // 检查新邮箱是否已被其他用户使用
        $checkEmail = $conn->prepare("SELECT id FROM info WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $data['email'], $data['id']);
        $checkEmail->execute();
        $emailResult = $checkEmail->get_result();
        
        if ($emailResult->num_rows > 0) {
            die(json_encode(['success' => false, 'message' => 'email_exists']));
        }

        // 获取旧邮箱
        $stmt = $conn->prepare("SELECT email FROM info WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $oldEmail = $row['email'];
    }

    $updates = [];
    $params = [];
    $types = '';

    if (isset($data['realname']) && !empty($data['realname'])) {
        $updates[] = "realname = ?";
        $params[] = $data['realname'];
        $types .= "s";
    }

    if (isset($data['email']) && !empty($data['email'])) {
        $updates[] = "email = ?";
        $params[] = $data['email'];
        $types .= "s";

        // 如果邮箱发生变化，更新课表邮件数据库
        if ($oldEmail !== $data['email']) {
            $command = escapeshellcmd("python ../../auxiliaryProgram/courseEmailDB.py update " . 
                                    escapeshellarg($oldEmail) . " " . 
                                    escapeshellarg($data['email']));
            $output = [];
            $returnCode = -1;
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new Exception('课表邮件数据更新失败');
            }
        }
    }

    if (isset($data['password']) && !empty($data['password'])) {
        $updates[] = "password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        $types .= "s";
    }

    if (empty($updates)) {
        echo json_encode(['success' => true, 'message' => '没有需要更新的内容']);
        exit;
    }

    $params[] = $data['id'];
    $types .= "i";

    $sql = "UPDATE info SET " . implode(", ", $updates) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("准备语句失败: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => '更新成功']);
    } else {
        throw new Exception("更新失败: " . $stmt->error);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 