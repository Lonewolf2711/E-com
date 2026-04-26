<?php
/**
 * Frontend Contact Controller
 */

class FrontendContactController extends Controller
{
    public function index(): void
    {
        $this->render('frontend/contact', [
            'page_title' => 'Contact Us',
        ]);
    }

    public function submit(): void
    {
        Middleware::verifyCsrf();

        $name = trim($this->post('name', ''));
        $email = trim($this->post('email', ''));
        $subject = trim($this->post('subject', ''));
        $message = trim($this->post('message', ''));

        if (empty($name) || empty($email) || empty($message)) {
            Session::flash('error', 'Please fill in all required fields.');
            $this->redirect(url('/contact'));
            return;
        }

        // Save to database
        $contactModel = new ContactMessage();
        $contactModel->saveMessage($name, $email, $subject, $message);

        // Optionally notify admin via email
        $adminEmail = get_setting('admin_email');
        if (!empty($adminEmail)) {
            $body = "<h3>New Contact Message</h3>
                <p><strong>From:</strong> {$name} &lt;{$email}&gt;</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <hr>
                <div>" . nl2br(htmlspecialchars($message)) . "</div>";
            try {
                send_mail($adminEmail, "Contact: {$subject}", $body, '', $email);
            } catch (Exception $e) {
                error_log('Contact email failed: ' . $e->getMessage());
            }
        }

        Session::flash('success', 'Thank you for your message! We will get back to you soon.');
        $this->redirect(url('/contact'));
    }
}
