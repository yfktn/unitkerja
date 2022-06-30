# Unit Kerja dan Tulisan

Kegunaan plugin ini adalah untuk melakukan pemisahan user berdasarkan unit kerja, sehingga
untuk tulisan pun bisa dipisah recordnya.

## Mengapa membutuhkan plugin Tulisan?

Plugin ini dikembangkan karena kebutuhan tersebut, akibat plugin yang ada sebelumnya terlalu
rumit untuk digunakan.

Adapun plugin Tulisan ada di https://gitlab.com/yfktn/tulisan.

## Tentang Hak Akses

Saat ini user yang masuk dalam unit induk, tidak otomatis bisa mengakses ke unit yang menjadi
anakan induknya. Jadi hak akses pada Bagian A, tidak otomatis user tersebut punya akses ke
Subbagian B yang merupakan bagian dari Bagian A. Ini adalah keterbatasan pada versi saat ini.

Karena merupakan pengembangan Tulisan, jika user ingin memiliki hak akses ke tulisan user lain
dalam unitnya, maka yakinkan pula di otorisasi Tulisan user tersebut sudah punya hak untuk 
mengakses tulisan user yang lain. Demikian juga bila user ini memiliki hak akses ke unit yang 
lain, maka pastikan hak akses unit lain pada otorisasi plugin UnitKerja sudah diaktifkan.

## Versi OctoberCMS

Ini ditesting menggunakan octobercms versi 2.