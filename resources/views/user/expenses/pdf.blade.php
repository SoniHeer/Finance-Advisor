<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Expense Report | FinanceAI</title>

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        margin: 25px;
        color: #0f172a;
    }

    h1,h2,h3,h4 { margin:0; }

    .header-table {
        width:100%;
        border-bottom:2px solid #e5e7eb;
        padding-bottom:10px;
        margin-bottom:20px;
    }

    .brand {
        font-size:20px;
        font-weight:bold;
    }

    .brand span {
        color:#2563eb;
    }

    .meta {
        text-align:right;
        font-size:9px;
        color:#64748b;
    }

    .summary {
        width:100%;
        border-collapse:collapse;
        margin-bottom:18px;
    }

    .summary td {
        width:25%;
        border:1px solid #e5e7eb;
        background:#f8fafc;
        padding:10px;
        text-align:center;
    }

    .summary p {
        margin:0;
        font-size:9px;
        text-transform:uppercase;
        color:#64748b;
    }

    .summary h3 {
        margin-top:4px;
        font-size:14px;
    }

    .insight {
        border:1px solid #e5e7eb;
        background:#f9fafb;
        padding:12px;
        margin-bottom:20px;
        font-size:10px;
        line-height:1.6;
    }

    table.report {
        width:100%;
        border-collapse:collapse;
        margin-top:10px;
    }

    thead { display: table-header-group; }

    th {
        background:#f1f5f9;
        border:1px solid #cbd5e1;
        padding:6px;
        font-size:9px;
        text-transform:uppercase;
    }

    td {
        border:1px solid #e5e7eb;
        padding:6px;
        word-break: break-word;
    }

    tbody tr:nth-child(even) {
        background:#f9fafb;
    }

    .right { text-align:right; }

    tfoot th {
        background:#f8fafc;
        font-size:11px;
        font-weight:bold;
    }

    .footer {
        margin-top:30px;
        border-top:1px solid #e5e7eb;
        padding-top:8px;
        font-size:9px;
        text-align:center;
        color:#64748b;
    }

    .page-number:after {
        content: "Page " counter(page);
    }
</style>
</head>

<body>

@php
    $total = (float)($summary['total'] ?? 0);
    $count = (int)($summary['count'] ?? 0);
    $average = (float)($summary['average'] ?? 0);
    $highest = $summary['highest'] ?? null;
    $topCategory = $summary['topCategory'] ?? null;
    $scope = $summary['scope'] ?? 'Personal';
@endphp

{{-- HEADER --}}
<table class="header-table">
<tr>
    <td class="brand">
        Finance<span>AI</span>
    </td>
    <td class="meta">
        <strong>Expense Intelligence Report</strong><br>
        Report ID: {{ $reportId ?? 'N/A' }}<br>
        Generated {{ now()->format('d M Y, h:i A') }}<br>
        Scope: {{ $scope }}
    </td>
</tr>
</table>

{{-- SUMMARY --}}
<table class="summary">
<tr>
    <td>
        <p>Total Expenses</p>
        <h3>₹{{ number_format($total,2) }}</h3>
    </td>
    <td>
        <p>Transactions</p>
        <h3>{{ $count }}</h3>
    </td>
    <td>
        <p>Average</p>
        <h3>₹{{ number_format($average,2) }}</h3>
    </td>
    <td>
        <p>Top Category</p>
        <h3>{{ $topCategory ?? '—' }}</h3>
    </td>
</tr>
</table>

{{-- INSIGHT --}}
<div class="insight">
<strong>Executive Analysis</strong><br><br>

• Highest Expense:
{{ $highest
    ? $highest->title . ' (₹' . number_format($highest->amount,2) . ')'
    : 'N/A' }}<br>

• Dominant Category:
{{ $topCategory ?? 'None' }}<br>

• Spending Behavior:
@if($average > 5000)
    High average transaction value detected.
@elseif($average > 2000)
    Moderate spending behavior observed.
@else
    Controlled spending behavior observed.
@endif<br>

• Recommendation:
{{ $topCategory
    ? 'Review high concentration category to optimize financial efficiency.'
    : 'Spending appears balanced and diversified.' }}
</div>

{{-- TABLE --}}
<table class="report">
<thead>
<tr>
    <th width="5%">#</th>
    <th width="35%">Title</th>
    <th width="20%">Category</th>
    <th width="15%" class="right">Amount</th>
    <th width="15%">Date</th>
</tr>
</thead>

<tbody>
@forelse($expenses as $index => $expense)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $expense->title }}</td>
    <td>{{ $expense->category }}</td>
    <td class="right">₹{{ number_format((float)$expense->amount,2) }}</td>
    <td>{{ optional($expense->expense_date)->format('d M Y') }}</td>
</tr>
@empty
<tr>
    <td colspan="5" style="text-align:center;padding:12px;color:#64748b;">
        No expenses recorded.
    </td>
</tr>
@endforelse
</tbody>

@if($count > 0)
<tfoot>
<tr>
    <th colspan="3" class="right">Grand Total</th>
    <th class="right">₹{{ number_format($total,2) }}</th>
    <th></th>
</tr>
</tfoot>
@endif
</table>

<div class="footer">
© {{ date('Y') }} FinanceAI • Confidential Financial Document<br>
<span class="page-number"></span>
</div>

</body>
</html>
