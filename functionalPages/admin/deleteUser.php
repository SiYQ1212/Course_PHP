<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    die(json_encode(['success' => false, 'message' => '无权限']));
}

require_once '../../dbConfig.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 获取用户邮箱
    $emailStmt = $conn->prepare("SELECT email FROM info WHERE id = ?");
    $emailStmt->bind_param("i", $data['id']);
    $emailStmt->execute();
    $emailResult = $emailStmt->get_result();
    $emailRow = $emailResult->fetch_assoc();
    $email = $emailRow['email'];

    // 删除用户记录
    $stmt = $conn->prepare("DELETE FROM info WHERE id = ?");
    $stmt->bind_param("i", $data['id']);
    $result = $stmt->execute();
    
    if ($result) {
        // 执行课表邮件Python脚本
        $command = escapeshellcmd("python ../../auxiliaryProgram/courseEmailDB.py delete " . escapeshellarg($email));
        exec($command, $output, $returnCode);

        // 执行代理邮箱Python脚本
        $proxyCommand = escapeshellcmd("python ../../auxiliaryProgram/proxyEmailDB.py delete " . escapeshellarg($email));
        exec($proxyCommand, $proxyOutput, $proxyReturnCode);

        echo json_encode(['success' => true, 'message' => '删除成功']);
    } else {
        throw new Exception("删除失败: " . $stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 