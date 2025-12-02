<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

// Pastikan user login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'] ?? 'user';

// ambil data user terbaru
function get_user($koneksi, $id_user) {
    $stmt = $koneksi->prepare("SELECT id_user, username, nama_pengguna, password, role FROM users WHERE id_user = ?");
    $stmt->bind_param("s", $id_user);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res ? $res->fetch_assoc() : null;
}

$data = get_user($koneksi, $id_user);

// flags untuk modal
$showModalSuccess = "";   // "nama" atau "password"
$add_success = "";
$add_error = "";
// messages for admin management actions
$manage_success = "";
$manage_error = "";

/* ================= UPDATE NAMA ================= */
if (isset($_POST['update_nama'])) {
    $nama_pengguna = trim($_POST['nama_pengguna'] ?? '');
    if ($nama_pengguna === '') {
        // ignore empty
    } else {
        $stmt = $koneksi->prepare("UPDATE users SET nama_pengguna = ? WHERE id_user = ?");
        $stmt->bind_param("ss", $nama_pengguna, $id_user);
        if ($stmt->execute()) {
            $_SESSION['nama_pengguna'] = $nama_pengguna;
            $showModalSuccess = "nama";
            // reload data
            $data = get_user($koneksi, $id_user);
        } else {
          }
    }
}

/* ================= GANTI PASSWORD (PLAIN TEXT) ================= */
if (isset($_POST['ganti_password'])) {
    $password_baru = $_POST['password_baru'] ?? '';
    if ($password_baru !== '') {
        $stmt = $koneksi->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        $stmt->bind_param("ss", $password_baru, $id_user);
        if ($stmt->execute()) {
            $showModalSuccess = "password";
            // reload data
            $data = get_user($koneksi, $id_user);
        }
    }
}

/* ============== TAMBAH AKUN (HANYA ADMIN) ============== */
if ($role === 'admin' && isset($_POST['add_account'])) {
    $new_name = trim($_POST['new_nama'] ?? '');
    $new_user = trim($_POST['new_username'] ?? '');
    $new_pass = $_POST['new_password'] ?? '';
    $new_role = $_POST['new_role'] ?? 'user';

    // basic validation
    if ($new_name === '' || $new_user === '' || $new_pass === '') {
        $add_error = "Semua field wajib diisi.";
    } else {
        // cek duplicate username
        $cek = $koneksi->prepare("SELECT username FROM users WHERE username = ?");
        $cek->bind_param("s", $new_user);
        $cek->execute();
        $res = $cek->get_result();
        if ($res && $res->num_rows > 0) {
            $add_error = "Username sudah dipakai!";
        } else {
            $new_id = "mc-" . substr(md5(rand()), 0, 8);
            $stmt = $koneksi->prepare("INSERT INTO users (id_user, username, nama_pengguna, password, role) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $new_id, $new_user, $new_name, $new_pass, $new_role);
            if ($stmt->execute()) {
                $add_success = "Akun baru berhasil dibuat!";
        // refresh user list later
            } else {
                $add_error = "Gagal membuat akun: " . $koneksi->error;
            }
        }
    }
}

/* ============== UPDATE USER (ADMIN) ============== */
if ($role === 'admin' && isset($_POST['update_user'])) {
  $edit_id = $_POST['edit_id'] ?? '';
  $edit_name = trim($_POST['edit_nama'] ?? '');
  $edit_username = trim($_POST['edit_username'] ?? '');
  $edit_role = $_POST['edit_role'] ?? 'user';
  $edit_password = $_POST['edit_password'] ?? '';

  if ($edit_id === '') {
    $manage_error = "ID user tidak ditemukan.";
  } else {
    // check duplicate username for other users
    $cek = $koneksi->prepare("SELECT id_user FROM users WHERE username = ? AND id_user <> ?");
    $cek->bind_param("ss", $edit_username, $edit_id);
    $cek->execute();
    $r = $cek->get_result();
    if ($r && $r->num_rows > 0) {
      $manage_error = "Username sudah dipakai oleh akun lain.";
    } else {
      if ($edit_password !== '') {
        $stmt = $koneksi->prepare("UPDATE users SET username = ?, nama_pengguna = ?, password = ?, role = ? WHERE id_user = ?");
        $stmt->bind_param("sssss", $edit_username, $edit_name, $edit_password, $edit_role, $edit_id);
      } else {
        $stmt = $koneksi->prepare("UPDATE users SET username = ?, nama_pengguna = ?, role = ? WHERE id_user = ?");
        $stmt->bind_param("ssss", $edit_username, $edit_name, $edit_role, $edit_id);
      }
      if ($stmt->execute()) {
        $manage_success = "Informasi akun berhasil diperbarui.";
      } else {
        $manage_error = "Gagal memperbarui akun: " . $koneksi->error;
      }
    }
  }
}

/* ============== DELETE USER (ADMIN) ============== */
if ($role === 'admin' && isset($_POST['delete_user'])) {
  $del_id = $_POST['del_id'] ?? '';
  if ($del_id === '') {
    $manage_error = "ID user tidak ditemukan.";
  } elseif ($del_id === $id_user) {
    $manage_error = "Anda tidak dapat menghapus akun Anda sendiri.";
  } else {
    $stmt = $koneksi->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt->bind_param("s", $del_id);
    if ($stmt->execute()) {
      $manage_success = "Akun berhasil dihapus.";
    } else {
      $manage_error = "Gagal menghapus akun: " . $koneksi->error;
    }
  }
}

// fetch users for admin table
if ($role === 'admin') {
  $users = [];
  $res = $koneksi->query("SELECT id_user, username, nama_pengguna, role FROM users ORDER BY role DESC, username ASC");
  if ($res) {
    while ($r = $res->fetch_assoc()) $users[] = $r;
  }
}

?>

<div class="container-fluid">
  <div class="row">

    <!-- SIDEBAR -->
    <?php include('../includes/sidebar.php'); ?>

    <!-- KONTEN PROFIL -->
    <div class="col-10 p-4 page-content">

      <p>Kelola informasi akun Anda.</p>

      <div class="card p-4" style="max-width: 600px;">

        <!-- TAMPILAN DATA -->
        <div class="mb-3">
          <label class="form-label">Nama Anda Saat Ini</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_pengguna'] ?? '') ?>" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($data['username'] ?? '') ?>" disabled>
        </div>

        <!-- tombol ubah nama & password -->
        <div class="d-flex gap-2 mb-3">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNama">Ubah Nama</button>
          <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalPassword">Ganti Password</button>
          <?php if ($role === 'admin'): ?>
            <button class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#modalAddAccount">+ Tambah Akun Baru</button>
          <?php endif; ?>
        </div>

        <small class="text-muted">Role: <strong><?= htmlspecialchars($data['role'] ?? $role) ?></strong></small>

      </div>

      <!-- ADMIN: Users list -->
      <?php if ($role === 'admin'): ?>
        <div class="card p-4 mt-4">
          <h5>Manajemen Pengguna</h5>

          <?php if ($manage_error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($manage_error) ?></div>
          <?php endif; ?>
          <?php if ($manage_success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($manage_success) ?></div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-sm table-striped">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?= htmlspecialchars($u['nama_pengguna']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary me-1 btn-edit-user" 
                              data-id="<?= htmlspecialchars($u['id_user']) ?>"
                              data-username="<?= htmlspecialchars($u['username']) ?>"
                              data-name="<?= htmlspecialchars($u['nama_pengguna']) ?>"
                              data-role="<?= htmlspecialchars($u['role']) ?>"
                              >Edit</button>

                      <?php if ($u['id_user'] !== $id_user): ?>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Hapus akun ini?');">
                          <input type="hidden" name="del_id" value="<?= htmlspecialchars($u['id_user']) ?>">
                          <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                      <?php else: ?>
                        <button class="btn btn-sm btn-outline-secondary" disabled>Hapus</button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- MODAL: UBAH NAMA -->
<div class="modal fade" id="modalNama" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubah Nama Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Baru</label>
          <input type="text" name="nama_pengguna" class="form-control" value="<?= htmlspecialchars($data['nama_pengguna'] ?? '') ?>" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="update_nama" class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: GANTI PASSWORD -->
<div class="modal fade" id="modalPassword" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ganti Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Password Baru</label>
          <input type="password" name="password_baru" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="ganti_password" class="btn btn-success">Ubah Password</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: TAMBAH AKUN (Hanya admin) -->
<div class="modal fade" id="modalAddAccount" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Akun Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <?php if ($add_error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($add_error) ?></div>
          <?php endif; ?>

          <?php if ($add_success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($add_success) ?></div>
          <?php endif; ?>

          <input type="hidden" name="add_account" value="1">

          <div class="mb-2">
            <label class="form-label">Nama Pengguna</label>
            <input name="new_nama" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Username</label>
            <input name="new_username" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Password</label>
            <input name="new_password" type="password" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Role</label>
            <select name="new_role" class="form-select" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL SUKSES -->
<div class="modal fade" id="modalSuccess" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <h4 class="text-success mb-3">Berhasil!</h4>
      <p id="modalSuccessMsg"></p>
      <button class="btn btn-primary mt-3" data-bs-dismiss="modal">OK</button>
    </div>
  </div>
</div>

    <!-- MODAL: EDIT USER (Admin) -->
    <div class="modal fade" id="modalEditUser" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Akun</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="mb-2">
              <label class="form-label">Nama Pengguna</label>
              <input name="edit_nama" id="edit_nama" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Username</label>
              <input name="edit_username" id="edit_username" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Password (kosongkan jika tidak diubah)</label>
              <input name="edit_password" id="edit_password" type="password" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Role</label>
              <select name="edit_role" id="edit_role" class="form-select" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="update_user" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

<!-- Bootstrap bundle (Popper + JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // show modal success d
    <?php if ($showModalSuccess === "nama"): ?>
        var m = new bootstrap.Modal(document.getElementById('modalSuccess'));
        document.getElementById('modalSuccessMsg').textContent = "Nama berhasil diperbarui.";
        m.show();
    <?php elseif ($showModalSuccess === "password"): ?>
        var m = new bootstrap.Modal(document.getElementById('modalSuccess'));
        document.getElementById('modalSuccessMsg').textContent = "Password berhasil diganti.";
        m.show();
    <?php endif; ?>

    // show modalAddAccount jika add_success or add_error
    <?php if ($add_success || $add_error): ?>
        var ma = new bootstrap.Modal(document.getElementById('modalAddAccount'));
        ma.show();
    <?php endif; ?>
    
  // edit user button handler (populate modal)
  document.querySelectorAll('.btn-edit-user').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = this.getAttribute('data-id');
      var username = this.getAttribute('data-username') || '';
      var name = this.getAttribute('data-name') || '';
      var role = this.getAttribute('data-role') || 'user';
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_username').value = username;
      document.getElementById('edit_nama').value = name;
      document.getElementById('edit_role').value = role;
      document.getElementById('edit_password').value = '';
      var m = new bootstrap.Modal(document.getElementById('modalEditUser'));
      m.show();
    });
  });
});
</script>

<?php include('../includes/footer.php'); ?>
