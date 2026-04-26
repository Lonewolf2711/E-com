<?php
/**
 * Admin Payment Controller
 * ────────────────────────
 * Payment list and transaction views.
 */

class AdminPaymentController extends Controller
{
    /**
     * List all payments.
     */
    public function index(): void
    {
        $paymentModel = new Payment();
        $page = (int) ($this->get('page') ?: 1);

        $filters = [
            'status'    => $this->get('status') ?: '',
            'method'    => $this->get('method') ?: '',
            'date_from' => $this->get('date_from') ?: '',
            'date_to'   => $this->get('date_to') ?: '',
        ];

        $payments = $paymentModel->getAdminPayments($filters, $page, 20);

        // Statistics
        $distribution = $paymentModel->getMethodDistribution();

        $this->render('admin/payments/index', [
            'page_title'   => 'Payments',
            'payments'     => $payments,
            'filters'      => $filters,
            'distribution' => $distribution,
        ]);
    }

    /**
     * Transaction details / logs.
     */
    public function transactions(): void
    {
        $paymentModel = new Payment();
        $page = (int) ($this->get('page') ?: 1);

        $payments = $paymentModel->getAdminPayments([], $page, 50);

        $this->render('admin/payments/transactions', [
            'page_title'   => 'Transaction Log',
            'payments'     => $payments,
        ]);
    }
}
