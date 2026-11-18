-- Database untuk tugas: Sistem Informasi PHP dengan login kunci simetris & asimetris
CREATE DATABASE IF NOT EXISTS ummi_kripto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ummi_kripto;

DROP TABLE IF EXISTS data_terenkripsi;
DROP TABLE IF EXISTS admin_keys;

CREATE TABLE admin_keys (
  id INT PRIMARY KEY,
  sym_hash VARCHAR(255) NOT NULL COMMENT 'password_hash dari kunci simetris',
  kdf_salt VARCHAR(64) NOT NULL COMMENT 'salt PBKDF2 (base64)',
  public_key TEXT NOT NULL COMMENT 'RSA public key (PEM)'
) ENGINE=InnoDB;

CREATE TABLE data_terenkripsi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(100) NOT NULL,
  isi_aes TEXT NOT NULL COMMENT 'AES-256-GCM (base64)',
  isi_rsa TEXT NOT NULL COMMENT 'RSA-2048 OAEP (base64)',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO admin_keys (id, sym_hash, kdf_salt, public_key) VALUES (1, '$2y$10$oEn2BHHx2LaBL28saA3dF.L1T/bYTsnWm7m.SgaT8Z6cT/ECh89x2', '/rMTsFboOxntktSDDYoGIA==', '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3KOtaXyR1iY7Sw8qhUSh
yc4Ez2kAEUFlJczlgP4/VP/nWAfrqrc1k3uvb0uUDw/jhdpRdsIP7ym5x0Uu9tMM
T7KT3fRhz4TUFJK79gTN/ManF/LTtjuV5NX1LZ8sfpI/RbQDaT+akDPk03Df2Lpa
ezKWWqOUiFF+TfsPagsRH6Ne9o36NrCHtRObtyfPISti1p9nEi7aoAlymGUJofLf
yXu1d8QgU8lohlOb1wmwvip61w0Q/05bd1My0ty3FVbcHh5AUkZ8Cs+g0v++sbyK
FhXJrfpKeg7MQa3h9qM2H1vBZJcXIBRVBLVJzLErtLB6iFZNrkwCV2VEx2vWxu0l
qwIDAQAB
-----END PUBLIC KEY-----
');

INSERT INTO data_terenkripsi (judul, isi_aes, isi_rsa) VALUES ('Data Mahasiswa 1', '9YKsFj/m1dy05hyHxqi11MqNDXsN4k3fsGT4lo7HUsiQUN7JroakfcPJ9GxxhptH+mUmfwMCurVZrxqX8wGKpz6LZIaf47jrssvUZd5ULw8j', 'CQhHT1Bhef0b/lV0zpzZzIzheBeuYYbP9BYxFRjGWsxSMEUnYPjmWhTDRZc8mZfK2c1SpNZMQtfnpTaPRUkCMlV0O9jPB/9FYdwonjHZNSsT3fF+bxa+o6yZreQniQNpe4s1YWy3zWnj4tiVp2n2w3otfrWLASmEo/QMa/CoJn9uv3thVk/eultuAx0o534puVErh7QvDVknyukZRhphVd33jHJyV+2cFGEdlXtV9LbEi6h+sBvAYDtlzu4Tf0a/RiSazMvnRsJA0ur9VvagNZ62wsm6p/JIA7z17Q5BkOM2aVK850oXTcH4RYNdHtV2nrGdf8haUq2SfWdpOxNcPA==');
INSERT INTO data_terenkripsi (judul, isi_aes, isi_rsa) VALUES ('Data Mahasiswa 2', 'L8WOIKPtQPWJhBS6HYlgIapy4MYwmAjy12Rn7ChfVU5by1ThbU98TlhKCQP2W1nJa0L7s12+De+TV6/fuGKFQigbKt80JJJvu320vZSZ2YaJMl+nxcA=', 'cLuK09cu1cTjff8wAiQPfFa3TKVJNZUsjLEasp8bnO8RF//l0eHM6aBeQ82j1I0xxqWE7godtDgByz5pXbC0Z1AtHlFA1o7k6mVmFFyraPgO4S7B0oZShAYyfySEl2JmGcPXB1xy/PdIQPFCwj8ZtyJuDKDvqX/JiincTdDf6vHW8dXlxwVGbTpaWYob1bQ4KMZtI33ezjGEaKasVZYkdD5Z14Rg6cvb9t8Ghy7sZV+kNq0UalU2V+KfrR2Zu2tTA8RF3+LmR+Jj5DPPUK9lAvUNoTaeo//lt8aSvNDmc5y/2wBTc3ot3+71e4YTmYUOFhgnS1qQ67f9dYxHm0mNKg==');
INSERT INTO data_terenkripsi (judul, isi_aes, isi_rsa) VALUES ('Data Mahasiswa 3', '9+M16DCMrdXbeZd/xtzMCWCJcfhp+DcPuRoGgEs5hlHK1wIQlF5k4+0nGyTn0KMvr7DepJU1XwaPFUdRujjFxuUOZVP5ZU9xEb+FNeHtWY7AiQ==', 'qQSS7BIYf5HUF5AJ/v754M/om3MR1a/wCdhW2Di7A0r3fIlGRYN3sX7lQ5qN2PPHxY4745iBnukeOxTsMrqzZ5C652CZHaKiOCfGm/WLORTm6fzu+HwNBrZ839tiHSA/Y8/Qld+Uk+int6pXc1uQ4wazzdAanKvtklJ28o179qM8Kc66bCo1494uYJz9wEnf1lzsS+98AAeWG6r1ISTC5T6LnH/yaysER5y2/A4hR/OQtq20RV6lLFd0JGnN/ZCbcwIBr4RD485PWSFhhOyVhNuw8uv9pjLcP3zgf22FK+v7UY66OT2ouzLcaGjNtVwWg9PYLZKenC8QoL0oizzyZw==');
INSERT INTO data_terenkripsi (judul, isi_aes, isi_rsa) VALUES ('Catatan Rahasia', 'HP3EosRQVGvrDA/SrkCQIaH86WPl1tOkvvpuD3p5hYdPSrhQZt7FPOh5asdKMb7KN4QHLR4a33/zkBD2oKKkm8CFFbLcVSsI+gDnfcW/vawrAw==', '2RMtA1OQWHf9DAF/jUO/QfNUXKrcFO7QSLtS27W2JrsS7RogJrBOxfwC37Ouj4BSQwcnzc4hHHl+boeR1t2XcDE4G3Ewb8z67snUG+WDXUPfR+nRbXzK8vYsiSO9JxkEcaM673BtQeMmYoFO8w21BvyK5lVivd9WoFLf7XflKZYOVHmltP5PPCdN0X8phBX0NHC19/qq0Ll3TfFmyMYOpJ1g+YnoA5dYIhPRojigBOHZWZl8Clc15jn/cqsHp1G2BBmL3DSoOjauV2l9Mxa2HfwV4m9SNOtv8KbjCwWvJX9JGN/gsBgDVUOEwxnlmbK4nrFuN21SfU56uNVgXhh1zA==');
