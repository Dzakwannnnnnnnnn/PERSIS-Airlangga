-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 02, 2026 at 06:21 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perizinan_siswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izins`
--

CREATE TABLE `izins` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_izin` datetime DEFAULT NULL,
  `jenis_izin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alasan_izin` text COLLATE utf8mb4_unicode_ci,
  `bukti_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paraf_siswa` tinyint(1) NOT NULL DEFAULT '0',
  `paraf_guru` tinyint(1) NOT NULL DEFAULT '0',
  `nama_guru_validator` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','diterima','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `izins`
--

INSERT INTO `izins` (`id`, `user_id`, `nama`, `kelas`, `waktu_izin`, `jenis_izin`, `alasan_izin`, `bukti_foto`, `paraf_siswa`, `paraf_guru`, `nama_guru_validator`, `keterangan`, `status`, `created_at`, `updated_at`) VALUES
(2, 8, 'test5', 'XI PPLG', '2026-02-26 19:00:00', 'sakit', 'fggf', 'bukti-izin/Kroyc5XB9pErKUjSxc5dwlEpC4MnISYxscuAl7x9.png', 1, 1, NULL, 'fggf', 'diterima', '2026-02-26 03:00:48', '2026-02-26 03:02:19'),
(3, 8, 'test5', 'XI PPLG', '2026-02-04 19:20:00', 'sakit', 'dsadwaw', 'bukti-izin/3of3kNpUEQJPNKpm5ptcS5R29fw5uamp4asCi2Nz.jpg', 1, 0, NULL, 'dsadwaw', 'pending', '2026-02-26 03:20:44', '2026-02-26 03:20:44'),
(4, 8, 'test5', 'XI PPLG', '2026-02-17 21:43:00', 'izin keluarga', 'dssad', NULL, 1, 0, NULL, 'dssad', 'pending', '2026-02-26 05:43:09', '2026-02-26 05:43:09'),
(5, 8, 'dzakwan', 'XI PPLG', '2026-02-26 22:50:00', 'izin keluarga', 'wawawawa', 'bukti-izin/QqtrOoaOPbP58Tjs4ypLnU8uvtbbgftRGhVtHQUw.png', 1, 1, NULL, 'wawawawa', 'diterima', '2026-02-26 06:50:41', '2026-02-26 06:56:37'),
(8, 15, 'Muhammad Dzakwan', 'XI', '2026-03-02 13:21:00', 'sakit', 'sakit', 'bukti-izin/crKMqBzFV3EKHWdlFw1uISYRWP3KmeBIwCOvOqCW.jpg', 1, 1, NULL, 'sakit', 'diterima', '2026-03-01 21:21:52', '2026-03-01 21:22:11'),
(9, 15, 'Muhammad Dzakwan', 'XI', '2026-03-01 13:26:00', 'sakit', 'sakit demam', 'bukti-izin/lg7tg8jeTI8gKXqfUdC32nwKIMG226uI9KUhfVw5.jpg', 1, 1, NULL, 'sakit demam', 'diterima', '2026-03-01 21:26:34', '2026-03-01 21:26:52'),
(10, 15, 'Muhammad Dzakwan', 'XI', '2026-03-01 13:28:00', 'sakit', 'sakit', 'bukti-izin/XpyWVxSuszKqKCw4yhdsWoSmaoWBK5NeXONrVF55.jpg', 1, 1, NULL, 'sakit', 'diterima', '2026-03-01 21:29:03', '2026-03-01 21:29:14'),
(11, 15, 'Muhammad Dzakwan', 'XI', '2026-03-02 13:37:00', 'keperluan mendesak', 'Rumah saya hanyut', NULL, 1, 1, NULL, 'Rumah saya hanyut', 'diterima', '2026-03-01 21:38:18', '2026-03-01 21:39:10'),
(12, 15, 'Muhammad Dzakwan', 'XI', '2026-03-02 13:42:00', 'sakit', 'fshgfdgh', NULL, 1, 1, NULL, 'fshgfdgh', 'diterima', '2026-03-01 21:42:25', '2026-03-01 21:43:18'),
(13, 15, 'Muhammad Dzakwan', 'XI', '2026-03-02 13:47:00', 'sakit', 'sakit', NULL, 1, 1, NULL, 'sakit', 'diterima', '2026-03-01 21:48:46', '2026-03-01 21:49:48'),
(14, 15, 'Muhammad Dzakwan', 'XI', '2026-03-02 14:06:00', 'sakit', 'jdjasjdas', NULL, 1, 1, 'Muhammad Yani', 'jdjasjdas', 'diterima', '2026-03-01 22:06:09', '2026-03-01 22:09:48');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_26_000238_add_role_to_users_table', 1),
(5, '2026_02_26_020000_create_izins_table', 2),
(6, '2026_02_26_010017_add_nip_to_users_table', 3),
(7, '2026_02_26_120000_add_detail_fields_to_izins_table', 4),
(8, '2026_02_26_130000_add_kelas_to_users_table', 5),
(9, '2026_03_02_000000_add_card_uid_to_users_table', 6),
(10, '2026_03_02_010000_add_nama_guru_validator_to_izins_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('EhI0VpJsAoOclkfMQph7eNogX9xJySPFtQxj1gqN', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS2lnd2lBRmV1QkZiRlBXNmtxc3BmTE50UmZkbUFqVlBDTncyUGVqTCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zdGF0dXMtcGVuZ2FqdWFuIjtzOjU6InJvdXRlIjtzOjExOiJpemluLnN0YXR1cyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE1O30=', 1772432008),
('wZVVyYlG4PNdjLpk38M1hN5ISd8aS7jhmhv86bJz', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY0ZYb2ZEZDRsZzhUdXhZSzZnWUZacU80Q054MWt4d2txekUxcDNiYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ndXJ1L3BlbmdhanVhbi8xNC9kb3dubG9hZC1wZGYiO3M6NToicm91dGUiO3M6MjI6Imd1cnUuaXppbi5kb3dubG9hZC1wZGYiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo5O30=', 1772432230);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_uid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siswa',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `nisn`, `kelas`, `card_uid`, `nip`, `phone`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `is_verified`) VALUES
(1, 'Muhammad Dzakwan', '9837491232', 'XI PPLG', NULL, NULL, '085822722058', 'dzakwan@sekulah.com', NULL, '$2y$12$sOWPi3N8P9bbIh9c4mrjku0bw1WcjmHroMlF98JjzSf1MhchDDHDW', NULL, '2026-02-26 01:22:22', '2026-02-26 06:05:41', 'siswa', 1),
(2, 'Muhammad Dzakwan', NULL, NULL, NULL, '242394134192', '085822722058', 'dzakwanepep@gmail.com', NULL, '$2y$12$8nMhFCWzRgwZXWDHsJX1IuQHZQkzVIdxLr0ikH2ArHr2i.wW0Fu.2', NULL, '2026-02-26 01:25:21', '2026-02-26 06:08:19', 'guru', 1),
(8, 'test5', '8765432345', 'XI PPLG', NULL, NULL, '085822722058', 'test5@sekulah.com', NULL, '$2y$12$hDP20ZZ6vLUMbciOsYilvO4/vGYIhIgd7sB0Dm9OZdCbRvsU0kaua', NULL, '2026-02-26 02:38:14', '2026-02-26 06:50:41', 'siswa', 1),
(9, 'wan', NULL, NULL, NULL, '76546870', '085822722058', 'guru@sekulah.com', NULL, '$2y$12$YpG0oP5iIkBl0M1ytcJvaOGTPXbXI3ZTpZQqcw7Alk.wzDwXF0LY6', NULL, '2026-02-26 03:02:07', '2026-02-26 05:46:32', 'guru', 1),
(10, 'Admin Sekolah', NULL, NULL, NULL, NULL, '081200000001', 'admin@sekolah.local', NULL, '$2y$12$4KncZOfuVCKcSckc7hNdH.Wi93rpDLo/F7r7PgK8fDeSYUVF3NGCO', 'fLknOaWlOp6VOA7VKeSOosWnR8egYYhOytzTMzmuPhs8u0WmkgVbljf2i588', '2026-02-26 05:45:12', '2026-02-26 05:45:12', 'admin', 1),
(15, 'Muhammad Dzakwan', '0096421430', 'XI', '2834234713', NULL, '085822722058', 'dzakwan@sekolah.smkti', NULL, '$2y$12$rN7.QFQZrnaiusx.el4y.u9NXiataFVloPiqMC1k9UImChsKxl.Ta', NULL, '2026-03-01 21:20:26', '2026-03-01 21:20:32', 'siswa', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `izins`
--
ALTER TABLE `izins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `izins_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nisn_unique` (`nisn`),
  ADD UNIQUE KEY `users_card_uid_unique` (`card_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `izins`
--
ALTER TABLE `izins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `izins`
--
ALTER TABLE `izins`
  ADD CONSTRAINT `izins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
