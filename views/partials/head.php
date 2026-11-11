<?php
/** @var string|null $title  @var string|null $description */
$siteName = config('app.name');
$pageTitle = isset($title) && $title ? $title . ' · ' . $siteName : $siteName . ' — Motorcycle parts & workshop in Kuang, Selangor';
$metaDescription = $description ?? 'Motorcycle spare parts, accessories, riding gear and workshop services in Kuang, Rawang. Serving Selangor riders since 2005.';
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDescription) ?>">
<meta name="theme-color" content="#111214">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($metaDescription) ?>">
<meta property="og:type" content="website">
<link rel="icon" href="<?= e(asset('assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800&display=swap">
<link rel="stylesheet" href="<?= e(asset('assets/css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/layout.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/components.css')) ?>">
