<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

// 獲取用戶 ID
$username = $_SESSION['username'];
$user_query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// 處理新增記帳
if (isset($_POST['add_expense'])) {
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $expense_date = $_POST['expense_date'];
    
    $insert_query = "INSERT INTO expenses (user_id, type, category, amount, description, expense_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("issdss", $user_id, $type, $category, $amount, $description, $expense_date);
    
    if ($stmt->execute()) {
        $success_message = ($type == 'income') ? "收入記錄成功！" : "支出記錄成功！";
    } else {
        $error_message = "記錄失敗：" . $stmt->error;
    }
}

// 處理刪除記帳
if (isset($_GET['delete'])) {
    $expense_id = $_GET['delete'];
    $delete_query = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ii", $expense_id, $user_id);
    $stmt->execute();
    header("Location: expense.php");
    exit();
}

// 獲取用戶的所有記帳記錄
$expenses_query = "SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, created_at DESC";
$stmt = $conn->prepare($expenses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expenses_result = $stmt->get_result();

// 計算總支出和總收入
$total_expense_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND type = 'expense'";
$stmt = $conn->prepare($total_expense_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_expense_result = $stmt->get_result();
$total_expense_row = $total_expense_result->fetch_assoc();
$total_expenses = $total_expense_row['total'] ?? 0;

$total_income_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND type = 'income'";
$stmt = $conn->prepare($total_income_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_income_result = $stmt->get_result();
$total_income_row = $total_income_result->fetch_assoc();
$total_income = $total_income_row['total'] ?? 0;

$balance = $total_income - $total_expenses;

// 按類別統計支出
$category_expense_query = "SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? AND type = 'expense' GROUP BY category";
$stmt = $conn->prepare($category_expense_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$category_expense_result = $stmt->get_result();

// 按類別統計收入
$category_income_query = "SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? AND type = 'income' GROUP BY category";
$stmt = $conn->prepare($category_income_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$category_income_result = $stmt->get_result();

// 月度統計
$monthly_query = "SELECT 
    DATE_FORMAT(expense_date, '%Y-%m') as month,
    type,
    SUM(amount) as total 
    FROM expenses 
    WHERE user_id = ? 
    GROUP BY month, type 
    ORDER BY month DESC 
    LIMIT 6";
$stmt = $conn->prepare($monthly_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$monthly_result = $stmt->get_result();

$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $month = $row['month'];
    if (!isset($monthly_data[$month])) {
        $monthly_data[$month] = ['income' => 0, 'expense' => 0];
    }
    $monthly_data[$month][$row['type']] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>記帳系統</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .expense-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #0093E9 0%, #80D0C7 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: white;
            margin: 0;
            padding: 0;
        }
        
        .header h1::after {
            display: none;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: white;
            color: #0056b3;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #0056b3;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #0093E9;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #0056b3, #0093E9);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card.income {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-card.expense {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        
        .stat-card.balance {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card h3 {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stat-card p {
            margin: 10px 0 0 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .expense-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .expense-table th {
            background-color: #0056b3;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .expense-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .expense-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .category-food { background-color: #ff6b6b; color: white; }
        .category-transport { background-color: #4ecdc4; color: white; }
        .category-entertainment { background-color: #95e1d3; color: #333; }
        .category-shopping { background-color: #f38181; color: white; }
        .category-bills { background-color: #aa96da; color: white; }
        .category-salary { background-color: #51cf66; color: white; }
        .category-bonus { background-color: #ffd93d; color: #333; }
        .category-investment { background-color: #845ec2; color: white; }
        .category-other { background-color: #fcbad3; color: #333; }
        
        .type-income {
            background-color: #d4f4dd;
            border-left: 4px solid #51cf66;
        }
        
        .type-expense {
            background-color: #ffe0e0;
            border-left: 4px solid #ff6b6b;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
        
        .export-btn {
            background-color: #17a2b8;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        
        .export-btn:hover {
            background-color: #138496;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: #0056b3;
            border-bottom-color: #0056b3;
            font-weight: 600;
        }
        
        .tab:hover {
            color: #0056b3;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .expense-table {
                font-size: 12px;
            }
            
            .expense-table th,
            .expense-table td {
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="expense-container">
        <div class="header">
            <div>
                <h1>💰 記帳系統</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">歡迎，<?php echo htmlspecialchars($_SESSION['username']); ?>！</p>
            </div>
            <div class="nav-buttons">
                <a href="export.php" class="btn export-btn">📊 導出 Excel</a>
                <a href="welcome.php" class="btn btn-primary">返回主頁</a>
                <a href="logout.php" class="btn btn-primary">登出</a>
            </div>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card income">
                <h3>💰 總收入</h3>
                <p>$<?php echo number_format($total_income, 2); ?></p>
            </div>
            <div class="stat-card expense">
                <h3>💸 總支出</h3>
                <p>$<?php echo number_format($total_expenses, 2); ?></p>
            </div>
            <div class="stat-card balance">
                <h3>💵 結餘</h3>
                <p>$<?php echo number_format($balance, 2); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>📝 記帳筆數</h3>
                <p><?php echo $expenses_result->num_rows; ?></p>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="card">
                <h2>新增記錄</h2>
                <form method="POST" action="expense.php" id="expenseForm">
                    <div class="form-group">
                        <label>類型</label>
                        <select name="type" id="typeSelect" required onchange="updateCategories()">
                            <option value="">請選擇類型</option>
                            <option value="expense">支出</option>
                            <option value="income">收入</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>類別</label>
                        <select name="category" id="categorySelect" required>
                            <option value="">請先選擇類型</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>金額</label>
                        <input type="number" name="amount" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>日期</label>
                        <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>說明</label>
                        <textarea name="description" placeholder="輸入說明..."></textarea>
                    </div>
                    
                    <button type="submit" name="add_expense" class="submit-btn">新增記錄</button>
                </form>
                
                <div style="margin-top: 30px;">
                    <div class="tabs">
                        <button class="tab active" onclick="switchTab('expense-stats')">支出統計</button>
                        <button class="tab" onclick="switchTab('income-stats')">收入統計</button>
                    </div>
                    
                    <div id="expense-stats" class="tab-content active">
                        <h3 style="color: #0056b3; margin-bottom: 10px;">支出類別統計</h3>
                        <?php if ($category_expense_result->num_rows > 0): ?>
                            <?php while ($cat = $category_expense_result->fetch_assoc()): ?>
                                <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                    <span class="category-badge category-<?php echo $cat['category']; ?>">
                                        <?php 
                                        $category_names = [
                                            'food' => '🍔 飲食',
                                            'transport' => '🚗 交通',
                                            'entertainment' => '🎮 娛樂',
                                            'shopping' => '🛍️ 購物',
                                            'bills' => '💡 帳單',
                                            'other' => '📌 其他'
                                        ];
                                        echo $category_names[$cat['category']] ?? $cat['category'];
                                        ?>
                                    </span>
                                    <strong>$<?php echo number_format($cat['total'], 2); ?></strong>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: #999; text-align: center;">暫無數據</p>
                        <?php endif; ?>
                    </div>
                    
                    <div id="income-stats" class="tab-content">
                        <h3 style="color: #0056b3; margin-bottom: 10px;">收入類別統計</h3>
                        <?php if ($category_income_result->num_rows > 0): ?>
                            <?php while ($cat = $category_income_result->fetch_assoc()): ?>
                                <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                    <span class="category-badge category-<?php echo $cat['category']; ?>">
                                        <?php 
                                        $income_category_names = [
                                            'salary' => '💼 薪水',
                                            'bonus' => '🎁 獎金',
                                            'investment' => '📈 投資',
                                            'other' => '📌 其他'
                                        ];
                                        echo $income_category_names[$cat['category']] ?? $cat['category'];
                                        ?>
                                    </span>
                                    <strong>$<?php echo number_format($cat['total'], 2); ?></strong>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: #999; text-align: center;">暫無數據</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="tabs">
                    <button class="tab active" onclick="switchMainTab('records')">記帳記錄</button>
                    <button class="tab" onclick="switchMainTab('monthly')">月度報表</button>
                    <button class="tab" onclick="switchMainTab('charts')">圖表分析</button>
                </div>
                
                <div id="records" class="tab-content active">
                    <h2>記帳記錄</h2>
                    <?php if ($expenses_result->num_rows > 0): ?>
                        <table class="expense-table">
                            <thead>
                                <tr>
                                    <th>類型</th>
                                    <th>日期</th>
                                    <th>類別</th>
                                    <th>金額</th>
                                    <th>說明</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $expenses_result->data_seek(0);
                                while ($expense = $expenses_result->fetch_assoc()): 
                                    $type_label = $expense['type'] == 'income' ? '收入' : '支出';
                                    $type_class = 'type-' . $expense['type'];
                                ?>
                                    <tr class="<?php echo $type_class; ?>">
                                        <td><strong><?php echo $type_label; ?></strong></td>
                                        <td><?php echo date('Y-m-d', strtotime($expense['expense_date'])); ?></td>
                                        <td>
                                            <span class="category-badge category-<?php echo $expense['category']; ?>">
                                                <?php 
                                                $all_categories = [
                                                    'food' => '🍔 飲食',
                                                    'transport' => '🚗 交通',
                                                    'entertainment' => '🎮 娛樂',
                                                    'shopping' => '🛍️ 購物',
                                                    'bills' => '💡 帳單',
                                                    'salary' => '💼 薪水',
                                                    'bonus' => '🎁 獎金',
                                                    'investment' => '📈 投資',
                                                    'other' => '📌 其他'
                                                ];
                                                echo $all_categories[$expense['category']] ?? $expense['category'];
                                                ?>
                                            </span>
                                        </td>
                                        <td><strong>$<?php echo number_format($expense['amount'], 2); ?></strong></td>
                                        <td><?php echo htmlspecialchars($expense['description'] ?: '-'); ?></td>
                                        <td>
                                            <a href="expense.php?delete=<?php echo $expense['id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirm('確定要刪除這筆記錄嗎？')"
                                               style="padding: 5px 10px; font-size: 12px;">
                                                刪除
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 10px;">📝</div>
                            <p>還沒有記帳記錄</p>
                            <p style="font-size: 14px;">開始記錄您的第一筆收支吧！</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="monthly" class="tab-content">
                    <h2>月度報表</h2>
                    <?php if (!empty($monthly_data)): ?>
                        <table class="expense-table">
                            <thead>
                                <tr>
                                    <th>月份</th>
                                    <th>收入</th>
                                    <th>支出</th>
                                    <th>結餘</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($monthly_data, true) as $month => $data): 
                                    $monthly_balance = $data['income'] - $data['expense'];
                                    $balance_color = $monthly_balance >= 0 ? '#51cf66' : '#ff6b6b';
                                ?>
                                    <tr>
                                        <td><strong><?php echo $month; ?></strong></td>
                                        <td style="color: #51cf66;"><strong>$<?php echo number_format($data['income'], 2); ?></strong></td>
                                        <td style="color: #ff6b6b;"><strong>$<?php echo number_format($data['expense'], 2); ?></strong></td>
                                        <td style="color: <?php echo $balance_color; ?>;"><strong>$<?php echo number_format($monthly_balance, 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 10px;">📊</div>
                            <p>暫無月度數據</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="charts" class="tab-content">
                    <h2>圖表分析</h2>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                    <div class="chart-container" style="margin-top: 40px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // 類別選擇動態更新
        function updateCategories() {
            const type = document.getElementById('typeSelect').value;
            const categorySelect = document.getElementById('categorySelect');
            
            const expenseCategories = [
                {value: 'food', text: '🍔 飲食'},
                {value: 'transport', text: '🚗 交通'},
                {value: 'entertainment', text: '🎮 娛樂'},
                {value: 'shopping', text: '🛍️ 購物'},
                {value: 'bills', text: '💡 帳單'},
                {value: 'other', text: '📌 其他'}
            ];
            
            const incomeCategories = [
                {value: 'salary', text: '💼 薪水'},
                {value: 'bonus', text: '🎁 獎金'},
                {value: 'investment', text: '📈 投資'},
                {value: 'other', text: '📌 其他'}
            ];
            
            categorySelect.innerHTML = '<option value="">請選擇類別</option>';
            
            const categories = type === 'expense' ? expenseCategories : (type === 'income' ? incomeCategories : []);
            
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.value;
                option.text = cat.text;
                categorySelect.appendChild(option);
            });
        }
        
        // 標籤切換
        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }
        
        function switchMainTab(tabName) {
            const tabs = document.querySelectorAll('.card > .tabs .tab');
            const contents = ['records', 'monthly', 'charts'];
            
            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(contentId => {
                const element = document.getElementById(contentId);
                if (element) element.classList.remove('active');
            });
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
            
            // 如果切換到圖表標籤，初始化圖表
            if (tabName === 'charts') {
                initCharts();
            }
        }
        
        // 初始化圖表
        function initCharts() {
            // 月度收支圖表
            const monthlyData = <?php echo json_encode(array_reverse($monthly_data, true)); ?>;
            const months = Object.keys(monthlyData);
            const incomeData = months.map(m => monthlyData[m].income || 0);
            const expenseData = months.map(m => monthlyData[m].expense || 0);
            
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx && !monthlyCtx.chartInstance) {
                monthlyCtx.chartInstance = new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: '收入',
                                data: incomeData,
                                backgroundColor: 'rgba(81, 207, 102, 0.6)',
                                borderColor: 'rgba(81, 207, 102, 1)',
                                borderWidth: 2
                            },
                            {
                                label: '支出',
                                data: expenseData,
                                backgroundColor: 'rgba(255, 107, 107, 0.6)',
                                borderColor: 'rgba(255, 107, 107, 1)',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '月度收支趨勢'
                            }
                        }
                    }
                });
            }
            
            // 類別支出圓餅圖
            <?php 
            $category_expense_result->data_seek(0);
            $cat_labels = [];
            $cat_data = [];
            while ($cat = $category_expense_result->fetch_assoc()) {
                $category_names = [
                    'food' => '🍔 飲食',
                    'transport' => '🚗 交通',
                    'entertainment' => '🎮 娛樂',
                    'shopping' => '🛍️ 購物',
                    'bills' => '💡 帳單',
                    'other' => '📌 其他'
                ];
                $cat_labels[] = $category_names[$cat['category']] ?? $cat['category'];
                $cat_data[] = $cat['total'];
            }
            ?>
            
            const categoryLabels = <?php echo json_encode($cat_labels); ?>;
            const categoryData = <?php echo json_encode($cat_data); ?>;
            
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx && !categoryCtx.chartInstance && categoryData.length > 0) {
                categoryCtx.chartInstance = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryData,
                            backgroundColor: [
                                'rgba(255, 107, 107, 0.8)',
                                'rgba(78, 205, 196, 0.8)',
                                'rgba(149, 225, 211, 0.8)',
                                'rgba(243, 129, 129, 0.8)',
                                'rgba(170, 150, 218, 0.8)',
                                'rgba(252, 186, 211, 0.8)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: '支出類別分布'
                            },
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
