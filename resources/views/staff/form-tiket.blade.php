@extends('layouts.staff')

@section('title', 'Form Report')
@vite(['resources/css/tiket.css', 'resources/js/report.js'])
@section('content')
<div class="container">
    <div class="form-container">
        <div class="header">
            <h1>Create Report</h1>
            <p>Submit your report with detailed information</p>
        </div>

        <div class="form-content">
            {{-- Success Message --}}
            @if(session('success'))
            <div class="success-message show">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Report submitted successfully!</strong>
                    <br>{{ session('success') }}
                </div>
            </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="help-text">
                <h4><i class="fas fa-info-circle"></i> Report Guidelines</h4>
                <p>Please provide accurate and detailed information. All reports will be reviewed and processed according to company policies.</p>
            </div>

            <form action="{{ route('report.submit') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf

                {{-- Reporter Information --}}
                <div class="section-title">
                    <i class="fas fa-user"></i>
                    Reporter Information
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" class="form-control @error('reporter_name') is-invalid @enderror" name="reporter_name"
                            id="reporterName" value="{{ old('reporter_name', auth()->user()->name ?? '') }}"
                            placeholder="Enter your full name" required>
                        @error('reporter_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control @error('reporter_email') is-invalid @enderror" name="reporter_email"
                            id="reporterEmail" value="{{ old('reporter_email', auth()->user()->email ?? '') }}"
                            placeholder="your.email@company.com" required>
                        @error('reporter_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Department <span class="required">*</span></label>
                        <select class="form-control @error('department') is-invalid @enderror" name="department"
                            id="department" required>
                            <option value="">Select your department</option>
                            <option value="hr" {{ old('department') == 'hr' ? 'selected' : '' }}>Human Resources</option>
                            <option value="finance" {{ old('department') == 'finance' ? 'selected' : '' }}>Finance</option>
                            <option value="marketing" {{ old('department') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="sales" {{ old('department') == 'sales' ? 'selected' : '' }}>Sales</option>
                            <option value="operations" {{ old('department') == 'operations' ? 'selected' : '' }}>Operations</option>
                            <option value="it" {{ old('department') == 'it' ? 'selected' : '' }}>Information Technology</option>
                            <option value="legal" {{ old('department') == 'legal' ? 'selected' : '' }}>Legal</option>
                            <option value="procurement" {{ old('department') == 'procurement' ? 'selected' : '' }}>Procurement</option>
                        </select>
                        @error('department')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Employee ID</label>
                        <input type="text" class="form-control @error('employee_id') is-invalid @enderror" name="employee_id"
                            id="employeeId" value="{{ old('employee_id') }}" placeholder="Your employee ID">
                        @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Report Details --}}
                <div class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Report Details
                </div>

                <div class="form-group">
                    <label class="form-label">Report Title <span class="required">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                        id="title" value="{{ old('title') }}" placeholder="Brief summary of the report" required
                        maxlength="150">
                    <div class="char-count" id="titleCount">{{ strlen(old('title', '')) }}/150</div>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Report Type <span class="required">*</span></label>
                        <select class="form-control @error('report_type') is-invalid @enderror" name="report_type"
                            id="reportType" required>
                            <option value="">Select report type</option>
                            <option value="incident" {{ old('report_type') == 'incident' ? 'selected' : '' }}>Incident Report</option>
                            <option value="safety" {{ old('report_type') == 'safety' ? 'selected' : '' }}>Safety Concern</option>
                            <option value="complaint" {{ old('report_type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="suggestion" {{ old('report_type') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                            <option value="violation" {{ old('report_type') == 'violation' ? 'selected' : '' }}>Policy Violation</option>
                            <option value="fraud" {{ old('report_type') == 'fraud' ? 'selected' : '' }}>Fraud/Ethics</option>
                            <option value="quality" {{ old('report_type') == 'quality' ? 'selected' : '' }}>Quality Issue</option>
                            <option value="other" {{ old('report_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('report_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Incident</label>
                        <input type="date" class="form-control @error('incident_date') is-invalid @enderror" name="incident_date"
                            id="incidentDate" value="{{ old('incident_date') }}">
                        @error('incident_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Detailed Description <span class="required">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                        id="description"
                        placeholder="Please provide a detailed description of the incident, situation, or concern. Include what happened, when it occurred, who was involved, and any relevant circumstances."
                        required maxlength="1000">{{ old('description') }}</textarea>
                    <div class="char-count" id="descriptionCount">{{ strlen(old('description', '')) }}/1000</div>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Location and People Involved --}}
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Location & People Involved
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Location <span class="required">*</span></label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" name="location"
                            id="location" value="{{ old('location') }}" placeholder="Building, floor, room, area" required>
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Time of Incident</label>
                        <input type="time" class="form-control @error('incident_time') is-invalid @enderror" name="incident_time"
                            id="incidentTime" value="{{ old('incident_time') }}">
                        @error('incident_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">People Involved</label>
                    <textarea class="form-control @error('people_involved') is-invalid @enderror" name="people_involved"
                        id="peopleInvolved"
                        placeholder="Names, titles, and departments of people involved (if applicable)"
                        maxlength="500">{{ old('people_involved') }}</textarea>
                    <div class="char-count" id="peopleCount">{{ strlen(old('people_involved', '')) }}/500</div>
                    @error('people_involved')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Witnesses</label>
                    <textarea class="form-control @error('witnesses') is-invalid @enderror" name="witnesses"
                        id="witnesses"
                        placeholder="Names and contact information of witnesses (if any)"
                        maxlength="500">{{ old('witnesses') }}</textarea>
                    <div class="char-count" id="witnessesCount">{{ strlen(old('witnesses', '')) }}/500</div>
                    @error('witnesses')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Severity Level --}}
                <div class="form-group">
                    <label class="form-label">Severity Level <span class="required">*</span></label>
                    <div class="severity-group">
                        <div class="severity-option">
                            <input type="radio" id="severity-low" name="severity" value="low" class="severity-radio"
                                {{ old('severity') == 'low' ? 'checked' : '' }} required>
                            <label for="severity-low" class="severity-label severity-low">
                                <div class="severity-icon">L</div>
                                <div class="severity-text">Low<br><small>Minor issue</small></div>
                            </label>
                        </div>
                        <div class="severity-option">
                            <input type="radio" id="severity-medium" name="severity" value="medium"
                                class="severity-radio" {{ old('severity') == 'medium' ? 'checked' : '' }}>
                            <label for="severity-medium" class="severity-label severity-medium">
                                <div class="severity-icon">M</div>
                                <div class="severity-text">Medium<br><small>Notable concern</small></div>
                            </label>
                        </div>
                        <div class="severity-option">
                            <input type="radio" id="severity-high" name="severity" value="high" class="severity-radio"
                                {{ old('severity') == 'high' ? 'checked' : '' }}>
                            <label for="severity-high" class="severity-label severity-high">
                                <div class="severity-icon">H</div>
                                <div class="severity-text">High<br><small>Serious issue</small></div>
                            </label>
                        </div>
                        <div class="severity-option">
                            <input type="radio" id="severity-critical" name="severity" value="critical"
                                class="severity-radio" {{ old('severity') == 'critical' ? 'checked' : '' }}>
                            <label for="severity-critical" class="severity-label severity-critical">
                                <div class="severity-icon">!</div>
                                <div class="severity-text">Critical<br><small>Immediate action</small></div>
                            </label>
                        </div>
                    </div>
                    @error('severity')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Supporting Evidence --}}
                <div class="section-title">
                    <i class="fas fa-paperclip"></i>
                    Supporting Evidence
                </div>

                <div class="form-group">
                    <label class="form-label">Attachments</label>
                    <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="file-upload-text">Click to upload files</div>
                        <div class="file-upload-hint">Photos, documents, recordings, or other evidence (max 10MB each)</div>
                        <input type="file" name="attachments[]" id="fileInput" class="file-input" multiple
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.mp4,.mov,.avi">
                    </div>
                    <div class="file-list" id="fileList"></div>
                    @error('attachments.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Actions Taken --}}
                <div class="form-group">
                    <label class="form-label">Immediate Actions Taken</label>
                    <textarea class="form-control @error('actions_taken') is-invalid @enderror" name="actions_taken"
                        id="actionsTaken"
                        placeholder="Describe any immediate actions you or others took in response to this incident"
                        maxlength="500">{{ old('actions_taken') }}</textarea>
                    <div class="char-count" id="actionsCount">{{ strlen(old('actions_taken', '')) }}/500</div>
                    @error('actions_taken')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Follow-up Options --}}
                <div class="section-title">
                    <i class="fas fa-cogs"></i>
                    Follow-up Preferences
                </div>

                <div class="form-group">
                    <label class="form-label">Preferred Contact Method</label>
                    <div class="contact-methods">
                        <label class="checkbox-label">
                            <input type="checkbox" name="contact_methods[]" value="email" 
                                {{ in_array('email', old('contact_methods', [])) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Email Updates
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="contact_methods[]" value="phone" 
                                {{ in_array('phone', old('contact_methods', [])) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Phone Call
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="contact_methods[]" value="meeting" 
                                {{ in_array('meeting', old('contact_methods', [])) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            In-Person Meeting
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number (if selected above)</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone"
                            id="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Anonymous Report?</label>
                        <select class="form-control @error('anonymous') is-invalid @enderror" name="anonymous" id="anonymous">
                            <option value="no" {{ old('anonymous') == 'no' ? 'selected' : '' }}>No, I want to be identified</option>
                            <option value="yes" {{ old('anonymous') == 'yes' ? 'selected' : '' }}>Yes, keep my identity confidential</option>
                        </select>
                        @error('anonymous')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i>
                        Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection