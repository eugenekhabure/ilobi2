<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Feedback - ILOBI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .feedback-card {
            background: white;
            border-radius: 24px;
            padding: 40px 36px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .feedback-card .logo { text-align: center; margin-bottom: 20px; }
        .feedback-card .logo img { height: 50px; }
        .feedback-card h2 { font-weight: 700; font-size: 24px; text-align: center; margin-bottom: 4px; }
        .feedback-card .subtitle { text-align: center; color: #64748b; font-size: 14px; margin-bottom: 24px; }
        .feedback-card .form-control { border-radius: 12px; padding: 12px 16px; border: 2px solid #e2e8f0; font-size: 15px; }
        .feedback-card .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79,70,229,0.15); }
        .feedback-card .btn-submit {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        .feedback-card .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79,70,229,0.35); }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 8px; }
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 40px;
            color: #e2e8f0;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .star-rating input:checked ~ label { color: #f59e0b; }
        .star-rating label:hover, .star-rating label:hover ~ label { color: #f59e0b; }
        .host-info { background: #f8fafc; border-radius: 12px; padding: 16px; text-align: center; margin-bottom: 20px; }
        .host-info .name { font-weight: 600; font-size: 18px; }
        .host-info .type { font-size: 13px; color: #64748b; }
    </style>
</head>
<body>

    <div class="feedback-card">
        <div class="logo">
            <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
        </div>
        <h2>📝 Share Your Experience</h2>
        <p class="subtitle">Your feedback helps us improve</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="host-info">
            <div class="name">{{ $visitingDetail->employee->user->name ?? 'Host' }}</div>
            <div class="type">Host • {{ $visitingDetail->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <form method="POST" action="{{ route('feedback.store') }}">
            @csrf
            <input type="hidden" name="visiting_detail_id" value="{{ $visitingDetail->id }}">

            <div class="mb-4 text-center">
                <label class="form-label fw-bold">Overall Rating</label>
                <div class="star-rating">
                    <input type="radio" name="rating" id="star5" value="5">
                    <label for="star5" title="Excellent">⭐</label>
                    <input type="radio" name="rating" id="star4" value="4">
                    <label for="star4" title="Good">⭐</label>
                    <input type="radio" name="rating" id="star3" value="3">
                    <label for="star3" title="Average">⭐</label>
                    <input type="radio" name="rating" id="star2" value="2">
                    <label for="star2" title="Poor">⭐</label>
                    <input type="radio" name="rating" id="star1" value="1">
                    <label for="star1" title="Very Poor">⭐</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Would you recommend us?</label>
                <div class="d-flex gap-3">
                    <label><input type="radio" name="would_recommend" value="1"> Yes</label>
                    <label><input type="radio" name="would_recommend" value="0"> No</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Your Comments</label>
                <textarea name="comment" class="form-control" rows="4" placeholder="Tell us about your experience..."></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Rate Your Experience</label>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Host Reception</label>
                    <select name="host_rating" class="form-select">
                        <option value="">Select</option>
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}">{{ $i }}⭐</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Security</label>
                    <select name="security_rating" class="form-select">
                        <option value="">Select</option>
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}">{{ $i }}⭐</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Cleanliness</label>
                    <select name="cleanliness_rating" class="form-select">
                        <option value="">Select</option>
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}">{{ $i }}⭐</option>
                        @endfor
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane me-2"></i>Submit Feedback
            </button>
        </form>
    </div>

</body>
</html>