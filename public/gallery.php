<?php
require_once __DIR__ . '/../bootstrap.php';
$userId    = requireAuth('gallery');
$canManage = hasPermission('gallery_manage');

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManage) {
    verifyCsrf();
    $action = post('action');

    try {
        if ($action === 'save_image') {
            $id       = (int) post('id');
            $caption  = post('caption');
            $category = post('category');
            $urlInput = post('url');

            if ($caption === '' || $category === '') {
                throw new RuntimeException('کیپشن اور کیٹیگری لازمی ہیں۔');
            }

            $uploadedPath = handleImageUpload('file', 'gallery');
            $finalUrl     = $uploadedPath ?: $urlInput;

            if ($id > 0) {
                Gallery::updateImage($id, $caption, $category, $uploadedPath ?: null);
            } else {
                if (!$finalUrl) {
                    throw new RuntimeException('براہ کرم تصویر اپ لوڈ کریں یا URL درج کریں۔');
                }
                Gallery::createImage($finalUrl, $caption, $category, $userId);
            }
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete_image') {
            Gallery::deleteImage((int) post('id'));
            $success = t_raw('msg_deleted');

        } elseif ($action === 'save_video') {
            $id          = (int) post('id');
            $caption     = post('caption');
            $category    = post('category');
            $youtubeIn   = post('youtube');

            if ($caption === '' || $category === '') {
                throw new RuntimeException('کیپشن اور کیٹیگری لازمی ہیں۔');
            }

            if ($id > 0) {
                Gallery::updateVideo($id, ['caption' => $caption, 'category' => $category]);
            } else {
                $uploadedPath = handleVideoUpload('file', 'videos');
                if ($uploadedPath) {
                    Gallery::createVideo([
                        'type' => 'upload', 'video_path' => $uploadedPath,
                        'caption' => $caption, 'category' => $category,
                    ], $userId);
                } elseif ($youtubeIn) {
                    $ytId = extractYoutubeId($youtubeIn);
                    if (!$ytId) {
                        throw new RuntimeException('یوٹیوب لنک درست نہیں لگتا۔');
                    }
                    Gallery::createVideo([
                        'type' => 'youtube', 'youtube_id' => $ytId,
                        'caption' => $caption, 'category' => $category,
                    ], $userId);
                } else {
                    throw new RuntimeException('براہ کرم یوٹیوب لنک درج کریں یا ویڈیو اپ لوڈ کریں۔');
                }
            }
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete_video') {
            Gallery::deleteVideo((int) post('id'));
            $success = t_raw('msg_deleted');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/gallery.php');
    }
}

$images     = Gallery::allImages();
$videos     = Gallery::allVideos();
$categories = Gallery::categories();

$pageTitle  = t_raw('gallery_heading');
$pageHero   = t('gallery_heading');
$activePage = 'gallery';
$content    = function () use ($images, $videos, $categories, $canManage, $errors) {
    require ROOT_PATH . '/views/pages/gallery.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
