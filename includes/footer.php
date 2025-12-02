</div> <!-- end content col -->
</div> <!-- row -->
</div> <!-- container -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
	const alerts = document.querySelectorAll('.alert.auto-dismiss');
	alerts.forEach(a=>{
		
		if (!a.querySelector('.btn-close')){
			const btn = document.createElement('button');
			btn.type = 'button'; btn.className = 'btn-close'; btn.setAttribute('aria-label','Close');
			btn.addEventListener('click', ()=> a.remove());
			a.appendChild(btn);
		}
		setTimeout(()=>{
			// fade out lalu remove
			a.style.transition = 'opacity 0.4s ease'; a.style.opacity = '0';
			setTimeout(()=> a.remove(), 400);
		}, 4000);
	});

	// Delete history modal 
	const deleteButtons = document.querySelectorAll('.btn-delete-history');
	const deleteModalEl = document.getElementById('confirmDeleteModal');
	if (deleteModalEl) {
		const bsModal = new bootstrap.Modal(deleteModalEl);
		deleteButtons.forEach(btn => {
			btn.addEventListener('click', function(e){
				const formId = this.getAttribute('data-form');
				// set form id 
				const confirmBtn = deleteModalEl.querySelector('#confirmDeleteBtn');
				confirmBtn.setAttribute('data-form', formId);
				bsModal.show();
			});
		});
		// confirm action
		deleteModalEl.querySelector('#confirmDeleteBtn').addEventListener('click', function(){
			const fid = this.getAttribute('data-form');
			const form = document.getElementById(fid);
			if (form) form.submit();
		});
	}
});

// Sidebar toggle 
document.addEventListener('DOMContentLoaded', function(){
	const toggle = document.querySelector('.sidebar-toggle');
	const sidebar = document.querySelector('.sidebar');
	if (!toggle || !sidebar) return;
	// buat overlay
	let overlay = document.createElement('div');
	overlay.className = 'sidebar-overlay d-none';
	overlay.style.position = 'fixed';
	overlay.style.inset = '0';
	overlay.style.background = 'rgba(0,0,0,0.35)';
	overlay.style.zIndex = '1040';
	overlay.style.transition = 'opacity 0.2s ease';
	document.body.appendChild(overlay);

	function openSidebar(){ sidebar.classList.add('open'); overlay.classList.remove('d-none'); overlay.style.opacity = '1'; }
	function closeSidebar(){ sidebar.classList.remove('open'); overlay.style.opacity = '0'; setTimeout(()=> overlay.classList.add('d-none'), 200); }

	toggle.addEventListener('click', function(){
		if (sidebar.classList.contains('open')) closeSidebar(); else openSidebar();
	});
	overlay.addEventListener('click', closeSidebar);
});
</script>
<!-- Confirm hapus modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				Apakah Anda yakin ingin menghapus riwayat perhitungan ini? Tindakan ini tidak dapat dikembalikan.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="button" id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
			</div>
		</div>
	</div>
</div>
</body>
</html>
