<?php
/**
 * Admin Contact Controller
 * ─────────────────────────
 * Manages contact form messages from the website's contact page.
 */

class AdminContactController extends Controller
{
    private ContactMessage $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactMessage();
    }

    /**
     * List all contact messages.
     */
    public function index(): void
    {
        $page   = (int) ($_GET['page'] ?? 1);
        $search = trim($_GET['q'] ?? '');
        $filter = trim($_GET['filter'] ?? '');

        $messages    = $this->contactModel->getAll($search, $page, 20, $filter);
        $unreadCount = $this->contactModel->countUnread();

        $this->render('admin/contacts/index', [
            'page_title'   => 'Contact Messages',
            'messages'     => $messages,
            'unread_count' => $unreadCount,
            'filters'      => [
                'search' => $search,
                'filter' => $filter,
            ],
        ]);
    }

    /**
     * View a single contact message (marks it as read).
     */
    public function detail(int $id): void
    {
        $message = $this->contactModel->getById($id);

        if (!$message) {
            Session::flash('error', 'Message not found.');
            $this->redirect(url('/admin/contacts'));
            return;
        }

        // Auto-mark as read on view
        if (!$message['is_read']) {
            $this->contactModel->markRead($id);
            $message['is_read'] = 1;
        }

        $this->render('admin/contacts/detail', [
            'page_title' => 'Message from ' . e($message['name']),
            'message'    => $message,
        ]);
    }

    /**
     * Toggle read/unread status via POST.
     */
    public function toggleRead(int $id): void
    {
        $message = $this->contactModel->getById($id);
        if (!$message) {
            Session::flash('error', 'Message not found.');
            $this->redirect(url('/admin/contacts'));
            return;
        }

        if ($message['is_read']) {
            $this->contactModel->markUnread($id);
            Session::flash('success', 'Message marked as unread.');
        } else {
            $this->contactModel->markRead($id);
            Session::flash('success', 'Message marked as read.');
        }

        $this->redirect(url("/admin/contacts/{$id}"));
    }

    /**
     * Delete a contact message.
     */
    public function delete(int $id): void
    {
        $this->contactModel->delete($id);
        Session::flash('success', 'Message deleted.');
        $this->redirect(url('/admin/contacts'));
    }
}
