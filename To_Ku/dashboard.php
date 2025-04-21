<!-- dashboard.php - Main todolist page after login -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once "db.php";

// Fetch tasks for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tasks WHERE user_id = ? ORDER BY tanggal_mulai ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Todolist App</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="logo">
                <img src="ChatGPT_Image_Apr_22__2025__12_02_39_AM-removebg-preview.png" alt="Logo" class="logo-img">
            </div>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="search.....">
            </div>
            <div class="user-info">
                <span>To Ku</span>
                <a href="auth.php?logout=1" class="logout-btn">Logout</a>
            </div>
        </header>
        
        <div class="content">
            <div class="task-controls">
                <button id="tambahTugasBtn" class="btn btn-primary">TAMBAH TUGAS</button>
            </div>
            
            <div class="task-table">
                <table>
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Judul</th>
                            <th>deskripsi</th>
                            <th>Status</th>
                            <th>Tanggal mulai</th>
                            <th>Tanggal selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $counter++ . '.</td>';
                            echo '<td>' . htmlspecialchars($row['judul']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['deskripsi']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['tanggal_mulai']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['tanggal_selesai']) . '</td>';
                            echo '<td class="action-buttons">';
                            echo '<button class="edit-btn" data-id="' . $row['id'] . '" data-judul="' . htmlspecialchars($row['judul']) . '" 
                                data-deskripsi="' . htmlspecialchars($row['deskripsi']) . '" data-status="' . htmlspecialchars($row['status']) . '" 
                                data-tanggal-mulai="' . htmlspecialchars($row['tanggal_mulai']) . '" data-tanggal-selesai="' . htmlspecialchars($row['tanggal_selesai']) . '">EDIT</button>';
                            echo '<button class="hapus-btn" data-id="' . $row['id'] . '">HAPUS</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        
                        if ($result->num_rows === 0) {
                            echo '<tr><td colspan="7" class="no-data">No tasks found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add Task Modal -->
    <div id="addTaskModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Tambah Tugas</h2>
            <form id="addTaskForm" action="task_handler.php" method="POST">
                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" name="judul" id="judul" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="Belum">Belum</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>
                </div>
                <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_task" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Task Modal -->
    <div id="editTaskModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Tugas</h2>
            <form id="editTaskForm" action="task_handler.php" method="POST">
                <input type="hidden" name="task_id" id="edit_task_id">
                <div class="form-group">
                    <label for="edit_judul">Judul</label>
                    <input type="text" name="judul" id="edit_judul" required>
                </div>
                <div class="form-group">
                    <label for="edit_deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select name="status" id="edit_status">
                        <option value="Belum">Belum</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required>
                </div>
                <div class="form-group">
                    <label for="edit_tanggal_selesai">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="update_task" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteTaskModal" class="modal">
        <div class="modal-content delete-modal">
            <h2>Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus tugas ini?</p>
            <div class="confirm-buttons">
                <form id="deleteTaskForm" action="task_handler.php" method="POST">
                    <input type="hidden" name="task_id" id="delete_task_id">
                    <button type="button" id="cancelDelete" class="btn btn-secondary">Batal</button>
                    <button type="submit" name="delete_task" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>