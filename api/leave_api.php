<?php
require_once "db.php";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $rows = $pdo->query("SELECT l.*, u.fullname FROM leave_forms l JOIN users u ON l.user_id = u.user_id ORDER BY l.leave_id DESC")->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(["success" => true, "data" => $rows]);
  exit();
}
$in = json_decode(file_get_contents("php://input"), true) ?? [];
$act = $in['action'] ?? 'create';
if ($act === 'login') {
  $st = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
  $st->execute([$in['username']]);
  $u = $st->fetch(PDO::FETCH_ASSOC);
  if ($u && (password_verify($in['password'], $u['password']) || $in['password'] === $u['password'])) {
    unset($u['password']);
    echo json_encode(["success" => true, "user" => $u]);
  } else {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "เข้าสู่ระบบไม่สำเร็จ"]);
  }
} elseif ($act === 'create') {
  $pdo->prepare("INSERT INTO leave_forms (user_id, leave_type, date_from, date_to, reason) VALUES (?,?,?,?,?)")
      ->execute([intval($in['user_id']), $in['leave_type'], $in['date_from'], $in['date_to'], $in['reason']]);
  echo json_encode(["success" => true, "message" => "ยื่นใบลาเรียบร้อยแล้ว"]);
} elseif ($act === 'status') {
  $pdo->prepare("UPDATE leave_forms SET status=? WHERE leave_id=?")->execute([$in['status'], intval($in['leave_id'])]);
  echo json_encode(["success" => true, "message" => "อัปเดตสถานะเรียบร้อยแล้ว"]);
}
?>
