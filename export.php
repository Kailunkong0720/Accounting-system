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

// 獲取所有記帳記錄
$expenses_query = "SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC";
$stmt = $conn->prepare($expenses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expenses_result = $stmt->get_result();

// 設置 CSV 標頭
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=expense_report_' . date('Y-m-d') . '.csv');

// 創建輸出流
$output = fopen('php://output', 'w');

// 添加 BOM 以支持 Excel 正確顯示中文
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// 寫入標題行
fputcsv($output, ['日期', '類型', '類別', '金額', '說明', '創建時間']);

// 類別名稱映射
$all_categories = [
    'food' => '飲食',
    'transport' => '交通',
    'entertainment' => '娛樂',
    'shopping' => '購物',
    'bills' => '帳單',
    'salary' => '薪水',
    'bonus' => '獎金',
    'investment' => '投資',
    'other' => '其他'
];

// 寫入數據
while ($expense = $expenses_result->fetch_assoc()) {
    $type_label = $expense['type'] == 'income' ? '收入' : '支出';
    $category_label = $all_categories[$expense['category']] ?? $expense['category'];
    
    fputcsv($output, [
        $expense['expense_date'],
        $type_label,
        $category_label,
        number_format($expense['amount'], 2),
        $expense['description'],
        $expense['created_at']
    ]);
}

fclose($output);
exit();
?>
