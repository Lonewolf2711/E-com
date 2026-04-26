<?php
/**
 * Admin Enquiry Controller
 * ────────────────────────
 * Management of customer enquiries in the admin dashboard.
 */

class AdminEnquiryController extends Controller
{
    private Enquiry $enquiryModel;

    public function __construct()
    {
        $this->enquiryModel = new Enquiry();
    }

    /**
     * List all enquiries grouped/paginated.
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $search = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? '');
        
        $enquiries = $this->enquiryModel->getAll($search, $page, 20, $status);
        
        // Build status counts for the pill buttons
        $enquiryStats = $this->enquiryModel->getStats();
        $statusCounts = [
            'new'          => $enquiryStats['new_enquiries']          ?? 0,
            'acknowledged' => $enquiryStats['acknowledged_enquiries'] ?? 0,
            'quoted'       => $enquiryStats['quoted_enquiries']       ?? 0,
            'closed'       => $enquiryStats['closed_enquiries']       ?? 0,
        ];

        $this->render('admin/enquiries/index', [
            'page_title'    => 'Manage Enquiries',
            'enquiries'     => $enquiries,
            'status_counts' => $statusCounts,
            'filters'       => [
                'search'    => $search,
                'status'    => $status,
                'date_from' => trim($_GET['date_from'] ?? ''),
                'date_to'   => trim($_GET['date_to']   ?? ''),
            ],
        ]);
    }

    /**
     * Detail View for specific enquiry.
     */
    public function detail(int $id): void
    {
        $enquiry = $this->enquiryModel->getById($id);

        if (!$enquiry) {
            Session::flash('error', 'Enquiry not found.');
            $this->redirect(url('/admin/enquiries'));
            return;
        }

        $cartItems = json_decode($enquiry['cart_snapshot'], true) ?: [];

        $this->render('admin/enquiries/detail', [
            'page_title' => 'Enquiry Details: ' . e($enquiry['enquiry_number']),
            'enquiry'    => $enquiry,
            'cart_items' => $cartItems,
        ]);
    }

    /**
     * Update enquiry status via POST.
     */
    public function updateStatus(int $id): void
    {
        $enquiry = $this->enquiryModel->getById($id);
        if (!$enquiry) {
            Session::flash('error', 'Enquiry not found.');
            $this->redirect(url('/admin/enquiries'));
            return;
        }

        $status = $this->post('status', '');
        $validStatuses = ['new', 'acknowledged', 'quoted', 'closed'];

        if (!in_array($status, $validStatuses)) {
            Session::flash('error', 'Invalid status.');
            $this->redirect(url("/admin/enquiries/{$id}"));
            return;
        }

        $this->enquiryModel->updateStatus($id, $status);
        Session::flash('success', 'Enquiry status updated successfully.');
        $this->redirect(url("/admin/enquiries/{$id}"));
    }

    /**
     * Send email manually / Groq Quote Email via POST.
     */
    public function sendEmail(int $id): void
    {
        $enquiryId = $id; // comes from the route parameter now
        $subject   = trim($this->post('subject', ''));
        $body      = trim($this->post('body', ''));

        $enquiry = $this->enquiryModel->getById($enquiryId);
        
        if (!$enquiry || empty($subject) || empty($body)) {
            Session::flash('error', 'Failed to send email. Invalid input.');
            $this->redirect(url('/admin/enquiries'));
            return;
        }

        try {
            $sent = send_mail($enquiry['customer_email'], $subject, nl2br(htmlspecialchars($body)));
        } catch (Exception $mailerEx) {
            error_log('send_mail failed: ' . $mailerEx->getMessage());
            $sent = false;
        }

        if ($sent) {
            Session::flash('success', 'Email dispatched successfully.');
            // Auto transition to quoted if they just sent a generated quote.
            $this->enquiryModel->updateStatus($enquiryId, 'quoted');
        } else {
            Session::flash('error', 'Mailer handler failed to dispatch the message.');
        }

        $this->redirect(url("/admin/enquiries/{$enquiryId}"));
    }

    /**
     * Delete an enquiry.
     */
    public function delete(int $id): void
    {
        $this->enquiryModel->delete($id);
        Session::flash('success', 'Enquiry deleted.');
        $this->redirect(url('/admin/enquiries'));
    }
}
