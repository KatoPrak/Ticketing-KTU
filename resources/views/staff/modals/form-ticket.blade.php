<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="ticketForm" class="row g-3" 
                action="{{ route('tickets.store') }}" 
                method="POST" 
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <div>
                        <h1 class="modal-title fs-4" id="createTicketModalLabel">
                            <i class="fas fa-ticket-alt me-2 text-primary"></i>
                            Buat Tiket Baru
                        </h1>
                        <p class="mb-0 opacity-75">Laporkan masalah atau ajukan permintaan bantuan.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label for="ticketName" class="form-label">Nama</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label for="ticketDept" class="form-label">Departemen</label>
                        <input type="text" class="form-control" 
                            value="{{ Auth::user()->department ?? 'Belum diatur' }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label for="ticketCategory" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="ticketEmail" class="form-label">Email Kontak</label>
                        <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                    </div>

                    <div class="col-12">
                        <label for="ticketDescription" class="form-label">Deskripsi Detail <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" 
                                placeholder="Jelaskan masalah Anda secara rinci..." 
                                style="height: 120px" required></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">
                            <i class="fas fa-paperclip me-1"></i>
                            Lampiran File <small class="text-muted">(opsional)</small>
                        </label>
                        <input type="file" name="attachments[]" multiple 
                            accept=".jpg,.jpeg,.png,.heif" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>