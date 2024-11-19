<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    header("Location: ../../login.php");
    exit();
}

// 检查是否是 AJAX 请求
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// 如果不是 AJAX 请求，输出完整的 HTML 头部
if (!$isAjax) {
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/common1.css">
    <meta charset="utf-8">
    <style>
        .user-table {
            width: 80%;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
            margin-bottom: 20px; /* 添加底部间距 */
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            min-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-content h3 {
            color: #333;
            margin: 0 0 30px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
            font-size: 1.8em;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .form-group label {
            width: 100px;
            margin-right: 15px;
            color: #555;
            font-weight: 500;
            text-align: right;
        }

        .form-group input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: flex-end;
            padding-left: 115px;
        }

        .modal-button {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-button i {
            font-size: 16px;
        }

        .edit-button {
            background: #4CAF50;
            color: white;
        }

        .delete-button {
            background: #f44336;
            color: white;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 28px;
            color: #999;
            cursor: pointer;
            transition: color 0.3s;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-modal:hover {
            color: #333;
            background: rgba(0, 0, 0, 0.1);
        }

        .success-toast {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .toast-content {
            background-color: white;
            padding: 20px 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: toastIn 0.3s ease-out;
        }

        .toast-content i {
            color: #4CAF50;
            font-size: 24px;
        }

        .toast-message {
            color: #333;
            font-size: 16px;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toast-hide {
            animation: toastOut 0.3s ease-in forwards;
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        /* 分页样式更新 */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .page-info {
            color: white;
            font-size: 14px;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            min-width: 120px;
            text-align: center;
        }

        /* 隐藏的按钮保持占位 */
        .page-link[style*="visibility: hidden"] {
            visibility: hidden;
        }

        .admin-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 5px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            text-decoration: none;
        }

        .admin-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .admin-button i {
            font-size: 18px;
        }
    </style>
</head>
<body>
<?php
}

require_once '../../dbConfig.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// 分页参数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // 每页显示10条
$start = ($page - 1) * $perPage;

// 获取总记
$countResult = $conn->query("SELECT COUNT(*) as total FROM info");
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// 修改查询以支持分页
$stmt = $conn->prepare("SELECT id, username, realname, email, created_at FROM info LIMIT ?, ?");
$stmt->bind_param("ii", $start, $perPage);
$stmt->execute();
$result = $stmt->get_result();

// 如果是 AJAX 请求，只返回表格内容
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    // 获取分页数据
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 10;
    $start = ($page - 1) * $perPage;

    // 获取总记录数
    $countResult = $conn->query("SELECT COUNT(*) as total FROM info");
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $perPage);

    // 获取当前页的数据
    $stmt = $conn->prepare("SELECT id, username, realname, email, created_at FROM info LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();

    // 只输出表格内容和分页
    ?>
    <table>
        <thead>
            <tr>
                <th>用户名</th>
                <th>真实姓名</th>
                <th>邮箱</th>
                <th>注册时间</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr onclick='showUserModal(<?php echo json_encode($row); ?>)'>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['realname']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <div class="pagination">
        <a href="?page=1" class="page-link first-page" <?php echo $page <= 1 ? 'style="visibility: hidden;"' : ''; ?>>
            <i class="fas fa-angle-double-left"></i>
        </a>
        <a href="?page=<?php echo ($page - 1); ?>" class="page-link" <?php echo $page <= 1 ? 'style="visibility: hidden;"' : ''; ?>>
            <i class="fas fa-angle-left"></i>
        </a>
        
        <div class="page-info">
            第 <?php echo $page; ?> 页 / 共 <?php echo $totalPages; ?> 页
        </div>
        
        <a href="?page=<?php echo ($page + 1); ?>" class="page-link" <?php echo $page >= $totalPages ? 'style="visibility: hidden;"' : ''; ?>>
            <i class="fas fa-angle-right"></i>
        </a>
        <a href="?page=<?php echo $totalPages; ?>" class="page-link last-page" <?php echo $page >= $totalPages ? 'style="visibility: hidden;"' : ''; ?>>
            <i class="fas fa-angle-double-right"></i>
        </a>
    </div>
    <?php
    exit;
}

// 如果不是 AJAX 请求，输出剩余的 HTML
?>
    <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
        <a href="../../welcome.php" class="admin-button">
            <i class="fas fa-sign-out-alt"></i>
            <span>退出</span>
        </a>
    <?php endif; ?>
    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>用户名</th>
                    <th>真实姓名</th>
                    <th>邮箱</th>
                    <th>注册时间</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick="showUserModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['realname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- 分页控件始终显示 -->
        <div class="pagination">
            <a href="?page=1" class="page-link first-page" <?php echo $page <= 1 ? 'style="visibility: hidden;"' : ''; ?>>
                <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="?page=<?php echo ($page - 1); ?>" class="page-link" <?php echo $page <= 1 ? 'style="visibility: hidden;"' : ''; ?>>
                <i class="fas fa-angle-left"></i>
            </a>
            
            <div class="page-info">
                第 <?php echo $page; ?> 页 / 共 <?php echo $totalPages; ?> 页
            </div>
            
            <a href="?page=<?php echo ($page + 1); ?>" class="page-link" <?php echo $page >= $totalPages ? 'style="visibility: hidden;"' : ''; ?>>
                <i class="fas fa-angle-right"></i>
            </a>
            <a href="?page=<?php echo $totalPages; ?>" class="page-link last-page" <?php echo $page >= $totalPages ? 'style="visibility: hidden;"' : ''; ?>>
                <i class="fas fa-angle-double-right"></i>
            </a>
        </div>
    </div>

    <!-- 用户管理模态框 -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3>用户管理</h3>
            <form id="userForm">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label>用户名：</label>
                    <input type="text" id="username" disabled>
                </div>
                <div class="form-group">
                    <label>真实姓名：</label>
                    <input type="text" id="realname">
                </div>
                <div class="form-group">
                    <label>邮箱地址：</label>
                    <input type="email" id="userEmail" placeholder="请输入新的邮箱地址">
                </div>
                <div class="form-group">
                    <label>新密码：</label>
                    <input type="password" id="userPassword" placeholder="留空表示不修改密码">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="modal-button edit-button" onclick="updateUser()">
                        <i class="fas fa-save"></i> 保存更改
                    </button>
                    <button type="button" class="modal-button delete-button" onclick="deleteUser()">
                        <i class="fas fa-trash"></i> 删除用户
                    </button>
                    <button type="button" class="modal-button cancel-button" onclick="closeModal()">
                        <i class="fas fa-times"></i> 取消
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="successToast" class="success-toast">
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span class="toast-message">更新成功</span>
        </div>
    </div>

    <script>
        function showUserModal(userData) {
            document.getElementById('userModal').style.display = 'block';
            document.getElementById('userId').value = userData.id;
            document.getElementById('username').value = userData.username;
            document.getElementById('realname').value = userData.realname;
            document.getElementById('userEmail').value = userData.email;
            document.getElementById('userPassword').value = '';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function showToast(message) {
            const toast = document.getElementById('successToast');
            const messageElement = toast.querySelector('.toast-message');
            messageElement.textContent = message;
            
            // 显示成功提示
            toast.style.display = 'flex';
            
            // 1秒后关闭所有模态框并刷新数据
            setTimeout(() => {
                toast.querySelector('.toast-content').classList.add('toast-hide');
                setTimeout(() => {
                    // 隐藏成功提示
                    toast.style.display = 'none';
                    toast.querySelector('.toast-content').classList.remove('toast-hide');
                    
                    // 关闭用户编辑模态框
                    const userModal = document.getElementById('userModal');
                    if (userModal) {
                        userModal.style.display = 'none';
                    }
                    
                    // 重新加载当前页面内容
                    const currentPage = new URLSearchParams(window.location.search).get('page') || '1';
                    loadPage(currentPage);
                }, 300);
            }, 1000);
        }

        function updateUser() {
            const userId = document.getElementById('userId').value;
            const realname = document.getElementById('realname').value;
            const email = document.getElementById('userEmail').value;
            const password = document.getElementById('userPassword').value;

            fetch('updateUser.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: userId,
                    realname: realname,
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('更新成功');
                } else {
                    alert(data.message || '更新失败');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('更新失败：请检查网络连接或联系管理员');
            });
        }

        function deleteUser() {
            const userId = document.getElementById('userId').value;

            fetch('deleteUser.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('删除成功');
                } else {
                    alert(data.message || '删除失败');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('删除失败：请检查网络连接或联系管理员');
            });
        }
        

        // 加载页面内容的函数
        function loadPage(page) {
            fetch(`adminPage.php?page=${page}&ajax=1`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    if (!html.trim()) {
                        throw new Error('Empty response');
                    }
                    
                    // 更新表格内容
                    const tableContainer = document.querySelector('.user-table');
                    if (tableContainer) {
                        tableContainer.innerHTML = html;
                        
                        // 更新 URL
                        window.history.pushState({page: page}, '', `?page=${page}`);
                        
                        // 重新绑定事件
                        bindUserRowEvents();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // 改为更友好的错误处理
                    const currentPage = new URLSearchParams(window.location.search).get('page') || '1';
                    window.location.href = `?page=${currentPage}`;
                });
        }

        // 绑定用户行点击事件
        function bindUserRowEvents() {
            document.querySelectorAll('tr[onclick]').forEach(row => {
                const originalOnClick = row.getAttribute('onclick');
                row.removeAttribute('onclick');
                row.addEventListener('click', function() {
                    eval(originalOnClick);
                });
            });
        }

        // 使用事件委托处理分页点击
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.page-link');
            if (pageLink) {
                e.preventDefault();
                const url = new URL(pageLink.href);
                const page = url.searchParams.get('page');
                if (page) {
                    loadPage(page);
                }
            }
        });

        // 初始化页面时绑定事件
        document.addEventListener('DOMContentLoaded', function() {
            bindUserRowEvents();
        });

        // 处理浏览器的前进/后退按钮
        window.addEventListener('popstate', function(e) {
            const page = new URLSearchParams(window.location.search).get('page') || '1';
            loadPage(page);
        });
    </script>
</body>
</html>