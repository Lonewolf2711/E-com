<?php
/**
 * Admin Banner Controller
 * ───────────────────────
 * CRUD for homepage banners (hero slider, side blocks).
 */

class AdminBannerController extends Controller
{
    private Banner $bannerModel;

    public function __construct()
    {
        $this->bannerModel = new Banner();
    }

    /**
     * List all banners.
     */
    public function index(): void
    {
        $banners = $this->bannerModel->getAllAdmin();

        $this->render('admin/banners/index', [
            'page_title' => 'Homepage Banners',
            'banners'    => $banners,
        ]);
    }

    /**
     * Show the add banner form.
     */
    public function addForm(): void
    {
        $this->render('admin/banners/form', [
            'page_title' => 'Add Banner',
            'banner'     => null,
        ]);
    }

    /**
     * Show the edit banner form.
     */
    public function editForm(int $id): void
    {
        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            Session::flash('error', 'Banner not found.');
            $this->redirect('/admin/banners');
            return;
        }

        $this->render('admin/banners/form', [
            'page_title' => 'Edit Banner',
            'banner'     => $banner,
        ]);
    }

    /**
     * Store a new banner.
     */
    public function store(): void
    {
        $title = trim($this->post('title', ''));
        $position = $this->post('position', 'hero');

        $data = [
            'position'    => $position,
            'title'       => $title,
            'subtitle'    => trim($this->post('subtitle', '')),
            'description' => trim($this->post('description', '')),
            'button_text' => trim($this->post('button_text', '')),
            'button_link' => trim($this->post('button_link', '')),
            'bg_color'    => trim($this->post('bg_color', '')),
            'sort_order'  => (int) $this->post('sort_order', 0),
            'status'      => $this->post('status', 'active'),
            'media_type'  => $this->post('media_type', 'image'),
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = handleUpload($_FILES['image'], 'banners');
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['path'];
            } else {
                Session::flash('error', 'Image upload failed: ' . $uploadResult['error']);
                $this->redirect('/admin/banners/add');
                return;
            }
        }

        // Handle video file upload (takes precedence over URL if both given)
        if (!empty($_FILES['video_file']['name'])) {
            $videoResult = $this->handleVideoUpload($_FILES['video_file']);
            if ($videoResult['success']) {
                $data['video_url'] = $videoResult['path'];
            } else {
                Session::flash('error', 'Video upload failed: ' . $videoResult['error']);
                $this->redirect('/admin/banners/add');
                return;
            }
        } elseif (trim($this->post('video_url', '')) !== '') {
            $data['video_url'] = trim($this->post('video_url', ''));
        }

        $this->bannerModel->create($data);

        Session::flash('success', 'Banner created successfully.');
        $this->redirect('/admin/banners');
    }

    /**
     * Update an existing banner.
     */
    public function update(int $id): void
    {
        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            Session::flash('error', 'Banner not found.');
            $this->redirect('/admin/banners');
            return;
        }

        $data = [
            'position'    => $this->post('position', 'hero'),
            'title'       => trim($this->post('title', '')),
            'subtitle'    => trim($this->post('subtitle', '')),
            'description' => trim($this->post('description', '')),
            'button_text' => trim($this->post('button_text', '')),
            'button_link' => trim($this->post('button_link', '')),
            'bg_color'    => trim($this->post('bg_color', '')),
            'sort_order'  => (int) $this->post('sort_order', 0),
            'status'      => $this->post('status', 'active'),
            'media_type'  => $this->post('media_type', 'image'),
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = handleUpload($_FILES['image'], 'banners');
            if ($uploadResult['success']) {
                // Delete old image
                if (!empty($banner['image'])) {
                    deleteUpload($banner['image']);
                }
                $data['image'] = $uploadResult['path'];
            } else {
                Session::flash('error', 'Image upload failed: ' . $uploadResult['error']);
                $this->redirect('/admin/banners/edit/' . $id);
                return;
            }
        }

        // Handle video file upload
        if (!empty($_FILES['video_file']['name'])) {
            $videoResult = $this->handleVideoUpload($_FILES['video_file']);
            if ($videoResult['success']) {
                $data['video_url'] = $videoResult['path'];
            } else {
                Session::flash('error', 'Video upload failed: ' . $videoResult['error']);
                $this->redirect('/admin/banners/edit/' . $id);
                return;
            }
        } elseif (trim($this->post('video_url', '')) !== '') {
            $data['video_url'] = trim($this->post('video_url', ''));
        }

        $this->bannerModel->update($id, $data);

        Session::flash('success', 'Banner updated successfully.');
        $this->redirect('/admin/banners');
    }

    /**
     * Delete a banner.
     */
    public function delete(int $id): void
    {
        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            Session::flash('error', 'Banner not found.');
            $this->redirect('/admin/banners');
            return;
        }

        // Delete image file
        if (!empty($banner['image'])) {
            deleteUpload($banner['image']);
        }
        // Delete video file if it was an upload (not a URL)
        if (!empty($banner['video_url']) && !filter_var($banner['video_url'], FILTER_VALIDATE_URL)) {
            deleteUpload($banner['video_url']);
        }

        $this->bannerModel->delete($id);

        Session::flash('success', 'Banner deleted successfully.');
        $this->redirect('/admin/banners');
    }

    /**
     * Handle video file upload.
     */
    private function handleVideoUpload(array $file): array
    {
        $allowedTypes = ['video/mp4', 'video/webm'];
        $maxSize = 50 * 1024 * 1024; // 50MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Only MP4 and WebM video formats are allowed.'];
        }
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Video file must be under 50MB.'];
        }

        $uploadDir = PUBLIC_PATH . '/uploads/banners/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('vid_', true) . '.' . strtolower($ext);
        $dest = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => 'Failed to move uploaded video file.'];
        }

        return ['success' => true, 'path' => 'banners/videos/' . $filename];
    }
}
