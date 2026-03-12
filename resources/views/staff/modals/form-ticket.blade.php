<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">
            <form id="createTicketForm" class="ticketForm" action="{{ route('staff.tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 bg-primary text-white">
                    <h1 class="modal-title fs-4 fw-bold" id="createTicketModalLabel">
                        <i class="fas fa-ticket-alt me-2"></i> Create New Ticket
                    </h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->department?->name ?? 'Not set' }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->location?->name ?? 'Not set' }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="staffCategorySelect" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                        </div>

                        {{-- Meeting Schedule Fields (hidden by default) --}}
                        <div class="col-12" id="staffMeetingFields" style="display: none;">
                            <div class="card border-primary border-opacity-25 shadow-sm rounded-4 overflow-hidden mb-2">
                                <div class="card-header bg-primary bg-gradient text-white py-3 border-0">
                                    <h6 class="card-title fw-bold mb-0 d-flex align-items-center">
                                        <i class="fas fa-calendar-alt fs-5 me-2"></i> Detail Meeting Schedule
                                    </h6>
                                    <p class="mb-0 small text-white-50 mt-1">Silakan lengkapi informasi jadwal meeting di bawah ini.</p>
                                </div>
                                <div class="card-body p-4 bg-light bg-opacity-50">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label fw-bold text-dark small text-uppercase">Topic/Judul Meeting <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg shadow-sm">
                                                <span class="input-group-text bg-white border-end-0 text-primary">
                                                    <i class="fas fa-comment-dots"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0 ps-0 meeting-field" id="staffMeetingTopic" placeholder="Contoh: Weekly Sync, Presentation...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase">Tanggal Meeting <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0 text-primary">
                                                    <i class="fas fa-calendar-day"></i>
                                                </span>
                                                <input type="date" class="form-control border-start-0 ps-0 meeting-field" id="staffMeetingDate">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase">Jam Mulai <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0 text-primary">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                                <input type="time" class="form-control border-start-0 ps-0 meeting-field" id="staffMeetingTime">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase">Durasi <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0 text-primary">
                                                    <i class="fas fa-hourglass-half"></i>
                                                </span>
                                                <select class="form-select border-start-0 ps-0 meeting-field" id="staffMeetingDuration">
                                                    <option value="" selected disabled>Pilih Durasi</option>
                                                    <option value="30 Menit">30 Menit</option>
                                                    <option value="1 Jam">1 Jam</option>
                                                    <option value="1.5 Jam">1.5 Jam</option>
                                                    <option value="2 Jam">2 Jam</option>
                                                    <option value="2.5 Jam">2.5 Jam</option>
                                                    <option value="3 Jam">3 Jam</option>
                                                    <option value="Unlimited">Unlimited</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="staffDescriptionField">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="staffDescriptionTextarea" style="height: 120px"></textarea>
                            <div class="mt-2" id="staffRemoteTip">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb text-warning me-1"></i>
                                    <strong>Remote Support Tip:</strong> Many issues can be resolved remotely. If possible, include your AnyDesk ID, or IP address to avoid unnecessary on-site visits.
                                </small>
                            </div>
                        </div>

                        {{-- Hidden default priority --}}
                        <input type="hidden" name="priority" value="low">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-paperclip me-1 text-secondary"></i> File Attachments
                                <small class="text-muted">(optional, .jpg .png .jpeg .heif)</small>
                            </label>
                            <input type="file" name="attachments[]" multiple accept="image/*" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const staffCategorySelect = document.getElementById('staffCategorySelect');
    const staffMeetingFields = document.getElementById('staffMeetingFields');
    const staffDescriptionTextarea = document.getElementById('staffDescriptionTextarea');
    const staffRemoteTip = document.getElementById('staffRemoteTip');

    if (staffCategorySelect) {
        staffCategorySelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text.trim().toLowerCase();
            const isMeeting = selectedText === 'meeting schedule';
            const staffDescriptionWrapper = document.getElementById('staffDescriptionField');

            if (isMeeting) {
                staffMeetingFields.style.display = '';
                // Tetap tampilkan description agar user bisa mengisi keterangan tambahan
                if (staffDescriptionWrapper) staffDescriptionWrapper.style.display = '';
                
                staffDescriptionTextarea.placeholder = 'Tambahkan keterangan tambahan (opsional)...';
                staffDescriptionTextarea.removeAttribute('required'); // opsional saat format meeting aktif
                
                // Make meeting fields required
                staffMeetingFields.querySelectorAll('.meeting-field').forEach(f => f.setAttribute('required', true));
            } else {
                staffMeetingFields.style.display = 'none';
                if (staffDescriptionWrapper) staffDescriptionWrapper.style.display = '';
                
                staffDescriptionTextarea.removeAttribute('readonly');
                staffDescriptionTextarea.style.backgroundColor = '';
                staffDescriptionTextarea.value = '';
                staffDescriptionTextarea.placeholder = '';
                staffDescriptionTextarea.setAttribute('required', true); // wajib jika bukan format meeting
                staffRemoteTip.style.display = '';
                // Remove required from meeting fields
                staffMeetingFields.querySelectorAll('.meeting-field').forEach(f => f.removeAttribute('required'));
            }
        });
    }

    // Intercept form submission to append Meeting Data payload softly
    const staffCreateForm = document.getElementById('createTicketForm');
    if (staffCreateForm) {
        staffCreateForm.addEventListener('submit', function(e) {
            const selectedText = staffCategorySelect?.options[staffCategorySelect.selectedIndex]?.text.trim().toLowerCase();
            if (selectedText === 'meeting schedule') {
                const topic = document.getElementById('staffMeetingTopic')?.value || '';
                const date = document.getElementById('staffMeetingDate')?.value || '';
                const time = document.getElementById('staffMeetingTime')?.value || '';
                const duration = document.getElementById('staffMeetingDuration')?.value || '';

                let formattedDate = date;
                if (date) {
                    const d = new Date(date);
                    formattedDate = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                }

                const meetingData = 
                    `- Topic/Judul Meeting : ${topic}\n` +
                    `- Tanggal Meeting : ${formattedDate}\n` +
                    `- Jam Mulai Meeting : ${time}\n` +
                    `- Durasi : ${duration}\n\n`;

                if (!staffDescriptionTextarea.value.startsWith('- Topic/Judul Meeting')) {
                    staffDescriptionTextarea.value = meetingData + staffDescriptionTextarea.value;
                }
            }
        });
    }

    // No more manual update mapping on input string since it's intercepted on submit


    // Reset meeting fields when modal is closed
    const createModal = document.getElementById('createTicketModal');
    if (createModal) {
        createModal.addEventListener('hidden.bs.modal', function() {
            staffMeetingFields.style.display = 'none';
            staffDescriptionTextarea.removeAttribute('readonly');
            staffDescriptionTextarea.style.backgroundColor = '';
            staffDescriptionTextarea.value = '';
            staffRemoteTip.style.display = '';
            // Clear meeting field values
            document.getElementById('staffMeetingTopic').value = '';
            document.getElementById('staffMeetingDate').value = '';
            document.getElementById('staffMeetingTime').value = '';
            document.getElementById('staffMeetingDuration').value = '';
            staffMeetingFields.querySelectorAll('.meeting-field').forEach(f => f.removeAttribute('required'));
        });
    }
});
</script>