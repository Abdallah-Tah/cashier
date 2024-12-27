<div>
    <h2>User Invoices</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Plan Name</th>
                <th>Total</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->number }}</td>
                    <td>{{ $invoice->payment->subscription->plan->name }}</td>
                    <td>{{ $invoice->total }} USD</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->due_date->format('F d, Y') }}</td>
                    <td>
                        <button wire:click="downloadInvoice({{ $invoice->payment->subscription->id }})">Download</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
