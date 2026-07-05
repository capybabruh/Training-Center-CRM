-- database/schema.sql
-- Training Center CRM - Lab06 Final

CREATE DATABASE IF NOT EXISTS web_php_lab06_crm
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE web_php_lab06_crm;

-- ============================================================
-- users: nhan vien tu van va admin he thong
-- ============================================================
CREATE TABLE users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100)  NOT NULL,
  email        VARCHAR(150)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role         ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  status       ENUM('active','inactive') NOT NULL DEFAULT 'active',
  last_login   DATETIME NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
);

-- ============================================================
-- leads: hoc vien tiem nang dang ky tu van
-- ============================================================
CREATE TABLE leads (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100)  NOT NULL,
  email        VARCHAR(150)  NOT NULL,
  phone        VARCHAR(30)   NULL,
  course_interest VARCHAR(150) NULL,
  status       ENUM('new','contacted','qualified','lost') NOT NULL DEFAULT 'new',
  note         TEXT NULL,
  assigned_to  INT NULL,
  source       VARCHAR(50) NULL,
  deleted_at   DATETIME NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_leads_email (email),
  INDEX idx_leads_status           (status),
  INDEX idx_leads_created_at       (created_at),
  INDEX idx_leads_status_created   (status, created_at),
  INDEX idx_leads_assigned         (assigned_to),
  INDEX idx_leads_deleted          (deleted_at)
);

-- ============================================================
-- orders: don thanh toan hoc phi
-- ============================================================
CREATE TABLE orders (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  order_code      VARCHAR(50)   NOT NULL,
  customer_name   VARCHAR(100)  NOT NULL,
  customer_email  VARCHAR(150)  NULL,
  course_name     VARCHAR(150)  NOT NULL,
  total_amount    DECIMAL(12,2) NOT NULL DEFAULT 0,
  paid_amount     DECIMAL(12,2) NOT NULL DEFAULT 0,
  status          ENUM('pending','paid','partial','cancelled') NOT NULL DEFAULT 'pending',
  note            TEXT NULL,
  lead_id         INT NULL,
  deleted_at      DATETIME NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_orders_code          (order_code),
  INDEX idx_orders_status            (status),
  INDEX idx_orders_created_at        (created_at),
  INDEX idx_orders_status_created    (status, created_at),
  INDEX idx_orders_customer_email    (customer_email),
  INDEX idx_orders_lead              (lead_id),
  INDEX idx_orders_deleted           (deleted_at)
);

-- ============================================================
-- order_payments: lich su thanh toan tung dot (bonus: transaction)
-- ============================================================
CREATE TABLE order_payments (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  order_id   INT NOT NULL,
  amount     DECIMAL(12,2) NOT NULL,
  method     ENUM('cash','bank_transfer','card') NOT NULL DEFAULT 'cash',
  note       VARCHAR(255) NULL,
  paid_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_payments_order (order_id),
  FOREIGN KEY (order_id) REFERENCES orders(id)
);
