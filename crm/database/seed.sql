-- database/seed.sql
-- Du lieu mau cho Training Center CRM

USE web_php_lab06_crm;

-- ============================================================
-- Users: tai khoan demo
-- Password thuc te cho ca 2 tai khoan: "password"
-- Hash duoi day duoc tao bang: password_hash('password', PASSWORD_DEFAULT)
-- ============================================================
INSERT INTO users (name, email, password_hash, role, status) VALUES
('Admin User',    'admin@crm.local', '$2y$10$wH8aQYz5x1S9b1pYV4xTluQwK7Z8mC2eD9fG1hJ3kL5mN7oP9qR1S', 'admin', 'active'),
('Nguyen Van An', 'an@crm.local',    '$2y$10$wH8aQYz5x1S9b1pYV4xTluQwK7Z8mC2eD9fG1hJ3kL5mN7oP9qR1S', 'staff', 'active');

-- LUU Y: Hash mau o tren CHI MANG TINH MINH HOA, chua chac dung dinh dang bcrypt that.
-- Truoc khi seed thuc te, hay tu sinh hash that bang lenh sau roi thay vao:
--   php -r "echo password_hash('password', PASSWORD_DEFAULT), PHP_EOL;"
-- Sau do UPDATE lai 2 dong tren voi hash vua sinh.

-- ============================================================
-- Leads
-- ============================================================
INSERT INTO leads (name, email, phone, course_interest, status, note, source) VALUES
('Anna Nguyen',    'anna@example.com',    '0909000001', 'PHP Web Development',  'new',       'Quan tam khoa PHP',            'facebook'),
('Ben Tran',       'ben@example.com',     '0909000002', 'Java Backend',         'contacted', 'Da goi lan 1',                 'google'),
('Chi Le',         'chi@example.com',     '0909000003', 'Python AI',            'qualified', 'Da dat lich hoc thu',          'walk-in'),
('Dung Pham',      'dung@example.com',    '0909000004', 'UI/UX Design',         'lost',      'Khong du tai chinh',           'facebook'),
('Minh Ho',        'minh@example.com',    '0909000005', 'Data Analytics',       'new',       'Form dang ky online',          'google'),
('Khoa Vo',        'khoa@example.com',    '0909000006', 'PHP Web Development',  'contacted', 'Hoi hoc phi',                  'zalo'),
('Linh Dang',      'linh@example.com',    '0909000007', 'Java Backend',         'new',       'SV nam cuoi',                  'facebook'),
('Nam Bui',        'nam@example.com',     '0909000008', 'Python AI',            'qualified', 'Muon hoc buoi toi',            'google'),
('Phuong Hoang',   'phuong@example.com',  '0909000009', 'DevOps',               'new',       'Da co kinh nghiem IT',         'walk-in'),
('Quang Huynh',    'quang@example.com',   '0909000010', 'PHP Web Development',  'contacted', 'Can tu van them',              'facebook'),
('Thu Phan',       'thu@example.com',     '0909000011', 'Mobile Flutter',       'new',       'Hoi qua facebook messenger',   'facebook'),
('Tuan Dao',       'tuan@example.com',    '0909000012', 'Data Analytics',       'lost',      'Chon trung tam khac',          'google'),
('Uyen Mai',       'uyen@example.com',    '0909000013', 'UI/UX Design',         'qualified', 'Hen tu van thu 2',             'zalo'),
('Viet Cao',       'viet@example.com',    '0909000014', 'Java Backend',         'contacted', 'Can xem demo',                 'walk-in'),
('Xuan Ly',        'xuan@example.com',    '0909000015', 'Python AI',            'new',       'Dang ky qua website',          'google'),
('Yen Truong',     'yen@example.com',     '0909000016', 'Mobile Flutter',       'contacted', 'Nhan vien dang cham soc',      'facebook'),
('Bao Nguyen',     'bao@example.com',     '0909000017', 'PHP Web Development',  'new',       'Hoc vien tiem nang',           'google'),
('Cam Thi',        'cam@example.com',     '0909000018', 'DevOps',               'qualified', 'Cong ty cu di hoc',            'walk-in'),
('Duc Ngo',        'duc@example.com',     '0909000019', 'Data Analytics',       'new',       'SV moi tot nghiep',            'facebook'),
('Giang Vu',       'giang@example.com',   '0909000020', 'UI/UX Design',         'contacted', 'Muon hoc cuoi tuan',           'zalo');

-- ============================================================
-- Orders
-- ============================================================
INSERT INTO orders (order_code, customer_name, customer_email, course_name, total_amount, paid_amount, status, note) VALUES
('ORD-2026-0001', 'Chi Le',       'chi@example.com',    'PHP Web Development',  8500000,  8500000,  'paid',      'Da thanh toan du'),
('ORD-2026-0002', 'Nam Bui',      'nam@example.com',    'Python AI',            12000000, 6000000,  'partial',   'Dat coc 50%'),
('ORD-2026-0003', 'Uyen Mai',     'uyen@example.com',   'UI/UX Design',         7500000,  0,        'pending',   'Cho xac nhan'),
('ORD-2026-0004', 'Cam Thi',      'cam@example.com',    'DevOps Essentials',    9800000,  9800000,  'paid',      'Cong ty thanh toan'),
('ORD-2026-0005', 'Anna Nguyen',  'anna@example.com',   'PHP Web Development',  8500000,  4250000,  'partial',   'Dat coc 50%'),
('ORD-2026-0006', 'Ben Tran',     'ben@example.com',    'Java Backend',         9200000,  0,        'pending',   'Cho hoc vien xac nhan'),
('ORD-2026-0007', 'Phuong Hoang', 'phuong@example.com', 'DevOps Essentials',    9800000,  9800000,  'paid',      'Thanh toan bank'),
('ORD-2026-0008', 'Khoa Vo',      'khoa@example.com',   'PHP Web Development',  8500000,  0,        'cancelled', 'Huy do khong sap xep duoc thoi gian'),
('ORD-2026-0009', 'Viet Cao',     'viet@example.com',   'Java Backend',         9200000,  9200000,  'paid',      ''),
('ORD-2026-0010', 'Linh Dang',    'linh@example.com',   'Java Backend',         9200000,  4600000,  'partial',   'Tra gop 2 dot'),
('ORD-2026-0011', 'Yen Truong',   'yen@example.com',    'Mobile Flutter',       10500000, 0,        'pending',   ''),
('ORD-2026-0012', 'Bao Nguyen',   'bao@example.com',    'PHP Web Development',  8500000,  8500000,  'paid',      ''),
('ORD-2026-0013', 'Giang Vu',     'giang@example.com',  'UI/UX Design',         7500000,  7500000,  'paid',      'Chuyen khoan'),
('ORD-2026-0014', 'Quang Huynh',  'quang@example.com',  'PHP Web Development',  8500000,  0,        'pending',   'Dang tu van'),
('ORD-2026-0015', 'Thu Phan',     'thu@example.com',    'Mobile Flutter',       10500000, 5250000,  'partial',   'Dat coc 50%'),
('ORD-2026-0016', 'Minh Ho',      'minh@example.com',   'Data Analytics',       11000000, 11000000, 'paid',      ''),
('ORD-2026-0017', 'Xuan Ly',      'xuan@example.com',   'Python AI',            12000000, 0,        'pending',   ''),
('ORD-2026-0018', 'Duc Ngo',      'duc@example.com',    'Data Analytics',       11000000, 0,        'cancelled', 'Thay doi ke hoach'),
('ORD-2026-0019', 'Chi Le',       'chi@example.com',    'Java Backend',         9200000,  9200000,  'paid',      'Dang ky khoa 2'),
('ORD-2026-0020', 'Nam Bui',      'nam@example.com',    'Data Analytics',       11000000, 0,        'pending',   'Dang cho');
