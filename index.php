<?php
session_start();
require_once 'config.php';
require_once 'clipboard.php';
$database = new Database();
$db = $database->getConnection();
$clipboard = new Clipboard($db);
// 处理表单提交
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $content = trim($_POST['content']);
                $title = trim($_POST['title']);
                if (!empty($content)) {
                    if ($clipboard->create($content, $title)) {
                        $message = '<div class="alert success">剪贴板项创建成功！</div>';
                    } else {
                        $message = '<div class="alert error">创建失败！</div>';
                    }
                }
                break;
            case 'update':
                $id = $_POST['id'];
                $content = trim($_POST['content']);
                $title = trim($_POST['title']);
                if (!empty($content)) {
                    if ($clipboard->update($id, $content, $title)) {
                        $message = '<div class="alert success">更新成功！</div>';
                    } else {
                        $message = '<div class="alert error">更新失败！</div>';
                    }
                }
                break;
            case 'delete':
                $id = $_POST['id'];
                if ($clipboard->delete($id)) {
                    $message = '<div class="alert success">删除成功！</div>';
                } else {
                    $message = '<div class="alert error">删除失败！</div>';
                }
                break;
        }
    }
}
// 获取所有剪贴板项
$items = $clipboard->getAll();
// 如果是编辑模式，获取要编辑的项
$editItem = null;
if (isset($_GET['edit'])) {
    $editItem = $clipboard->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线剪贴板</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
            font-family: monospace;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .items-list {
            margin-top: 40px;
        }
        
        .item-card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
        }
        
        .item-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .item-title {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        
        .item-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .item-content {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: monospace;
            font-size: 13px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .item-actions {
            display: flex;
            gap: 10px;
        }
        
        .expires-soon {
            color: #dc3545;
            font-weight: bold;
        }
        
        .no-items {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 在线剪贴板</h1>
        <p class="subtitle">数据将在创建后5小时自动过期删除</p>
        
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="<?php echo $editItem ? 'update' : 'create'; ?>">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="title">标题（可选）</label>
                <input type="text" id="title" name="title" 
                       placeholder="给你的剪贴板项起个名字..." 
                       value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="content">内容 *</label>
                <textarea id="content" name="content" 
                          placeholder="粘贴或输入你的内容..." 
                          required><?php echo $editItem ? htmlspecialchars($editItem['content']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn <?php echo $editItem ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editItem ? '✓ 更新' : '+ 创建新剪贴板项'; ?>
                </button>
                <?php if ($editItem): ?>
                    <a href="index.php" class="btn btn-cancel">取消</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="items-list">
            <h2 style="margin-bottom: 20px; color: #333;">所有剪贴板项</h2>
            
            <?php if (empty($items)): ?>
                <div class="no-items">
                    暂无剪贴板项，创建第一个吧！
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <?php 
                    $timeRemaining = $clipboard->getTimeRemaining($item['expires_at']);
                    $isExpiringSoon = $timeRemaining['hours'] < 1;
                    ?>
                    <div class="item-card">
                        <div class="item-header">
                            <div class="item-title">
                                <?php echo $item['title'] ? htmlspecialchars($item['title']) : '无标题'; ?>
                            </div>
                        </div>
                        
                        <div class="item-meta">
                            创建时间: <?php echo $item['created_at']; ?> | 
                            <span class="<?php echo $isExpiringSoon ? 'expires-soon' : ''; ?>">
                                剩余时间: <?php echo $timeRemaining['hours']; ?>小时 <?php echo $timeRemaining['minutes']; ?>分钟
                            </span>
                        </div>
                        
                        <div class="item-content">
<?php echo htmlspecialchars($item['content']); ?>
                        </div>
                        
                        <div class="item-actions">
                            <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-warning">✎ 编辑</a>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('确定要删除这个剪贴板项吗？');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger">✕ 删除</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>