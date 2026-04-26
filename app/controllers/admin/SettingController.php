<?php
/**
 * Admin Setting Controller
 * ────────────────────────
 * Manages site-wide settings (general, payment, shipping, social, etc.).
 */

class AdminSettingController extends Controller
{
    private Setting $settingModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
    }

    /**
     * Show all settings, grouped by category.
     */
    public function index(): void
    {
        $grouped = $this->settingModel->getAllGrouped();

        $this->render('admin/settings/index', [
            'page_title' => 'Site Settings',
            'grouped'    => $grouped,
        ]);
    }

    /**
     * Bulk update settings from the form.
     */
    public function update(): void
    {
        $settings = $this->post('settings', []);

        if (!is_array($settings) || empty($settings)) {
            $this->redirect('/admin/settings', 'No settings to update.', 'warning');
        }

        $this->settingModel->bulkUpdate($settings);
        Setting::clearCache();

        $this->redirect('/admin/settings', 'Settings updated successfully.', 'success');
    }
}
