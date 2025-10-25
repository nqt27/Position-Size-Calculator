<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Position Size Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Position Size Calculator</h1>

        {{-- errors --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('calculator.calculate') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Account Balance </label>
                            <input name="balance" type="number" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account Currency</label>
                            <select name="account_currency" class="form-select">
                                <option value="USD">USD</option>
                                <option value="EUR" >EUR</option>
                                <option value="GBP" >GBP</option>
                            </select>
                            
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Risk per Trade (%)</label>
                            <input name="risk_percent" type="number" step="0.01" min="0" max="100" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stop Loss (pips)</label>
                            <input name="stop_loss" type="number" step="0.01" min="0.01" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Currency Pair</label>
                            <select name="pair" class="form-select">
                                @php
                                $pairs = ['EURUSD','GBPUSD','USDJPY','GBPJPY','AUDUSD','EURJPY','XAUUSD'];
                                @endphp
                                <option value="">-- Chọn cặp tiền --</option>
                                @foreach($pairs as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>




                        <div class="col-12 mt-3">
                            <button class="btn btn-primary" type="submit">Caclulate</button>
                            <a class="btn btn-secondary" href="{{ route('calculator.index') }}">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Result --}}
        @if (!empty($result))
        <div class="card mb-4">
            <div class="card-header">
                Kết quả
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th>Account Balance</th>
                            <td>{{ $result['balance'] }} USD</td>
                        </tr>
                        <tr>
                            <th>Risk %</th>
                            <td>{{ $result['risk_percent'] }}%</td>
                        </tr>
                        <tr>
                            <th>Stop Loss</th>
                            <td>{{ $result['stop_loss'] }} pips</td>
                        </tr>
                        <tr>
                            <th>Currency Pair</th>
                            <td>{{ $result['pair'] }}</td>
                        </tr>
                        <tr>
                            <th>Risk Amount</th>
                            <td>{{ $result['risk_amount_usd'] }}</td>
                        </tr>
                        <tr>
                            <th>Pip Value</th>
                            <td>{{ $result['pip_value'] }}</td>
                        </tr>
                        <tr>
                            <th>Position Size (Lot)</th>
                            <td>{{ $result['position_size'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- History --}}
        <div class="card">
            <div class="card-header">Lịch sử tính toán</div>
            <div class="card-body p-0">
                @if ($history->isEmpty())
                <div class="p-3">Chưa có bản ghi nào.</div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Balance</th>
                                <th>Risk %</th>
                                <th>Stop Loss (pips)</th>
                                <th>Pair</th>
                                <th>Position Size</th>
                                <th>Created At</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $h)
                            <tr id="row-{{ $h->id }}">
                                <td>{{ $h->id }}</td>
                                <td>{{ number_format($h->balance,2) }}</td>
                                <td>{{ $h->risk_percent }}%</td>
                                <td>{{ $h->stop_loss }}</td>
                                <td>{{ $h->pair }}</td>
                                <td>{{ $h->position_size }}</td>
                                <td>{{ $h->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $h->id }}">
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $history->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: "Bạn có chắc chắn muốn xóa?",
                        text: "Thao tác này không thể hoàn tác!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Xóa",
                        cancelButtonText: "Hủy"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/calculations/${id}`, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        "Accept": "application/json"
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById(`row-${id}`).remove();
                                        Swal.fire("Đã xóa!", data.message, "success");
                                    } else {
                                        Swal.fire("Lỗi!", data.message, "error");
                                    }
                                })
                                .catch(() => Swal.fire("Lỗi!", "Không thể xóa bản ghi!", "error"));
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>