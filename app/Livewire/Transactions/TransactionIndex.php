<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Transaksi - Toko Barokah Jaya')]
class TransactionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $paymentMethodFilter = '';
    public $selectedTransaction;
    public $showDetailModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDetail($id)
    {
        $this->selectedTransaction = Transaction::with(['details.product', 'user'])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $query = Transaction::with('user')
            ->where('invoice_number', 'like', '%' . $this->search . '%');

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->paymentMethodFilter) {
            $query->where('payment_method', $this->paymentMethodFilter);
        }

        $transactions = $query->latest()->paginate(10);

        $todayTotal = Transaction::whereDate('created_at', today())->sum('total');
        $monthTotal = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $transactionCount = Transaction::count();

        return view('livewire.transactions.transaction-index', [
            'transactions' => $transactions,
            'todayTotal' => $todayTotal,
            'monthTotal' => $monthTotal,
            'transactionCount' => $transactionCount
        ]);
    }
}
