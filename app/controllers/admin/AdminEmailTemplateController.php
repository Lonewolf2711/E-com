<?php
/**
 * Admin Email Templates Controller
 * ─────────────────────────────────
 * CRUD for reusable email templates used in enquiry responses.
 */

class AdminEmailTemplateController extends Controller
{
    private EmailTemplate $templateModel;

    public function __construct()
    {
        $this->templateModel = new EmailTemplate();
    }

    /**
     * List all templates.
     */
    public function index(): void
    {
        $category  = trim($_GET['category'] ?? '');
        $templates = $this->templateModel->getAll($category);

        $this->render('admin/email_templates/index', [
            'page_title' => 'Email Templates',
            'templates'  => $templates,
            'category'   => $category,
        ]);
    }

    /**
     * Show create form.
     */
    public function createForm(): void
    {
        $this->render('admin/email_templates/form', [
            'page_title' => 'New Email Template',
            'template'   => [],
            'is_edit'    => false,
        ]);
    }

    /**
     * Store a new template.
     */
    public function store(): void
    {
        $name     = trim($this->post('name', ''));
        $subject  = trim($this->post('subject', ''));
        $body     = trim($this->post('body', ''));
        $category = trim($this->post('category', 'general'));

        if (empty($name) || empty($subject) || empty($body)) {
            Session::flash('error', 'Name, subject and body are required.');
            $this->redirect(url('/admin/email-templates/create'));
            return;
        }

        $this->templateModel->saveTemplate(compact('name', 'subject', 'body', 'category'));
        Session::flash('success', 'Template created successfully.');
        $this->redirect(url('/admin/email-templates'));
    }

    /**
     * Show edit form.
     */
    public function editForm(int $id): void
    {
        $template = $this->templateModel->getById($id);
        if (!$template) {
            Session::flash('error', 'Template not found.');
            $this->redirect(url('/admin/email-templates'));
            return;
        }

        $this->render('admin/email_templates/form', [
            'page_title' => 'Edit Template: ' . e($template['name']),
            'template'   => $template,
            'is_edit'    => true,
        ]);
    }

    /**
     * Update an existing template.
     */
    public function update(int $id): void
    {
        $name     = trim($this->post('name', ''));
        $subject  = trim($this->post('subject', ''));
        $body     = trim($this->post('body', ''));
        $category = trim($this->post('category', 'general'));

        if (empty($name) || empty($subject) || empty($body)) {
            Session::flash('error', 'Name, subject and body are required.');
            $this->redirect(url("/admin/email-templates/edit/{$id}"));
            return;
        }

        $this->templateModel->updateTemplate($id, compact('name', 'subject', 'body', 'category'));
        Session::flash('success', 'Template updated successfully.');
        $this->redirect(url('/admin/email-templates'));
    }

    /**
     * Delete a template.
     */
    public function delete(int $id): void
    {
        $this->templateModel->delete($id);
        Session::flash('success', 'Template deleted.');
        $this->redirect(url('/admin/email-templates'));
    }

    /**
     * JSON endpoint — used by jQuery on the enquiry detail page.
     * Returns subject + body with tokens resolved for an enquiry.
     */
    public function preview(int $id): void
    {
        $template = $this->templateModel->getById($id);
        if (!$template) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Template not found.']);
            exit;
        }

        $enquiryId = (int) ($_GET['enquiry_id'] ?? 0);
        $tokens    = [
            'store_name'    => get_setting('general_store_name', 'Our Store'),
            'enquiry_number' => '',
            'customer_name'  => '',
        ];

        if ($enquiryId) {
            $enquiry = (new Enquiry())->getById($enquiryId);
            if ($enquiry) {
                $tokens['enquiry_number'] = $enquiry['enquiry_number'] ?? '';
                $tokens['customer_name']  = $enquiry['customer_name']  ?? '';
            }
        }

        $resolved = $this->templateModel->resolve($template, $tokens);

        echo json_encode([
            'success' => true,
            'subject' => $resolved['subject'],
            'body'    => $resolved['body'],
        ]);
        exit;
    }
}
