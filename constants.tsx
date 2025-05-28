
import React from 'react';
import { Quest, Item, StageType, QuizQuestion } from './types';

// Heroicons (outline)
const CpuChipIcon: React.FC<React.SVGProps<SVGSVGElement>> = (props) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" {...props}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-7.5h12V7.5h-12v6Zm12 0V7.5m0 6V7.5m0 6v3.75m-12-3.75V7.5m0 6H3m1.5 0v3.75m13.5-3.75V7.5m0 6h1.5m-1.5 0v3.75M3 12h1.5M21 12h-1.5m-12 7.5h12V13.5h-12v6Zm12 0V13.5m0 6V13.5m0 6H3m1.5 0v-3.75m13.5 3.75V13.5m0 6h1.5m-1.5 0v-3.75M3 13.5h1.5M21 13.5h-1.5" />
  </svg>
);

const WifiIcon: React.FC<React.SVGProps<SVGSVGElement>> = (props) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" {...props}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.136 11.886c3.87-3.87 10.154-3.87 14.024 0M19.5 19.5c.164.163.31.33.445.5M2.25 19.5a20.326 20.326 0 0 1 4.822-7.845" />
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 20.25a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H12a.75.75 0 0 1-.75-.75V20.25Z" />
  </svg>
);

const GlobeAltIcon: React.FC<React.SVGProps<SVGSVGElement>> = (props) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" {...props}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A11.978 11.978 0 0 1 12 16.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3 12c0-.778.099-1.533.284-2.253m0 0A11.953 11.953 0 0 0 12 10.5c2.998 0 5.74 1.1 7.843 2.918M3 12c0 .778.099 1.533.284 2.253m0 0A11.978 11.978 0 0 0 12 16.5c2.998 0 5.74 1.1 7.843 2.918m-15.686 0A8.959 8.959 0 0 0 3 12Z" />
  </svg>
);

const ShieldCheckIcon: React.FC<React.SVGProps<SVGSVGElement>> = (props) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" {...props}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
  </svg>
);

const DevicePhoneMobileIcon: React.FC<React.SVGProps<SVGSVGElement>> = (props) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" {...props}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
  </svg>
);

const ServerStackIcon: React.FC<React.SVGProps<SVGSVGElement>> = (props) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" {...props}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M6 12L3.269 3.125A59.769 59.769 0 0 1 21.485 12H18M6 12v3.75m0-3.75L3.269 3.125A59.769 59.769 0 0 1 21.485 12H18m0 0h7.5v-1.5m-15 3.75v3.75m0-3.75L3.269 3.125A59.769 59.769 0 0 1 21.485 12H18m0 0h7.5v-1.5m-15 3.75L18 12m-12 3.75h7.5m-7.5 0L18 12" />
  </svg>
);

export const ITEMS: { [key: string]: Item } = {
  NETWORK_CABLE: { id: 'NETWORK_CABLE', name: 'Kabel Jaringan', description: 'Menghubungkan perangkat dalam jaringan lokal.', icon: <CpuChipIcon className="w-6 h-6 text-blue-400" /> },
  ROUTER_CONFIG: { id: 'ROUTER_CONFIG', name: 'Konfigurasi Router', description: 'Pengaturan untuk router agar berfungsi.', icon: <WifiIcon className="w-6 h-6 text-green-400" /> },
  IP_SCHEMA: { id: 'IP_SCHEMA', name: 'Skema IP Address', description: 'Rencana pengalamatan IP untuk jaringan.', icon: <GlobeAltIcon className="w-6 h-6 text-yellow-400" /> },
  PACKET_TRACER: { id: 'PACKET_TRACER', name: 'Pelacak Paket Data', description: 'Alat untuk melacak jalur paket data.', icon: <ServerStackIcon className="w-6 h-6 text-purple-400" /> },
  DNS_ADDRESS: { id: 'DNS_ADDRESS', name: 'Alamat Server DNS', description: 'Alamat server untuk translasi nama domain.', icon: <GlobeAltIcon className="w-6 h-6 text-orange-400" /> },
  SIGNAL_METER: { id: 'SIGNAL_METER', name: 'Pengukur Sinyal', description: 'Mengukur kekuatan sinyal seluler.', icon: <DevicePhoneMobileIcon className="w-6 h-6 text-sky-400" /> },
  BTS_BLUEPRINT: { id: 'BTS_BLUEPRINT', name: 'Cetak Biru BTS', description: 'Rancangan stasiun pemancar BTS.', icon: <WifiIcon className="w-6 h-6 text-red-400" /> },
  ENCRYPTED_CHIP: { id: 'ENCRYPTED_CHIP', name: 'Chip Data Terenkripsi', description: 'Menyimpan data rahasia dengan aman.', icon: <ShieldCheckIcon className="w-6 h-6 text-lime-400" /> },
  PHISHING_EVIDENCE: { id: 'PHISHING_EVIDENCE', name: 'Bukti Phishing', description: 'Contoh email phishing yang berhasil diidentifikasi.', icon: <ShieldCheckIcon className="w-6 h-6 text-pink-400" /> },
  BROWSER_CERTIFICATE: { id: 'BROWSER_CERTIFICATE', name: 'Sertifikat Keamanan Browser', description: 'Menandakan browser telah dikonfigurasi dengan aman.', icon: <ShieldCheckIcon className="w-6 h-6 text-teal-400" /> },
};

const QUEST_1_QUIZ: QuizQuestion[] = [
  { question: 'Apa fungsi utama sebuah Router dalam jaringan lokal?', options: ['Menghubungkan komputer ke internet', 'Membagi koneksi internet ke banyak perangkat', 'Menyimpan file', 'Mencetak dokumen'], correctAnswer: 'Membagi koneksi internet ke banyak perangkat', explanation: 'Router meneruskan paket data antar jaringan komputer dan memungkinkan banyak perangkat berbagi satu koneksi internet.' },
  { question: 'Perangkat keras apa yang digunakan untuk menghubungkan beberapa komputer dalam satu jaringan LAN secara fisik?', options: ['Modem', 'Switch/Hub', 'Printer', 'Scanner'], correctAnswer: 'Switch/Hub', explanation: 'Switch atau Hub adalah perangkat sentral yang menghubungkan semua perangkat di LAN.' },
];

const QUEST_2_QUIZ: QuizQuestion[] = [
  { question: 'Apa perbedaan mendasar antara Jaringan Lokal (LAN) dan Internet (WAN)?', options: ['LAN lebih cepat dari WAN', 'LAN mencakup area geografis kecil, WAN area besar (global)', 'Hanya WAN yang menggunakan kabel', 'LAN tidak aman, WAN aman'], correctAnswer: 'LAN mencakup area geografis kecil, WAN area besar (global)', explanation: 'LAN biasanya terbatas pada satu gedung atau kampus, sedangkan Internet (sebagai WAN) menghubungkan jaringan di seluruh dunia.' },
  { question: 'Apa fungsi utama proses "Routing" dalam jaringan komputer?', options: ['Mempercepat koneksi internet', 'Menentukan jalur terbaik untuk paket data mencapai tujuan', 'Mengenkripsi data', 'Memblokir virus'], correctAnswer: 'Menentukan jalur terbaik untuk paket data mencapai tujuan', explanation: 'Routing adalah proses memilih jalur di seluruh jaringan komputer untuk mengirimkan paket data.' },
];

const QUEST_3_QUIZ: QuizQuestion[] = [
    { question: 'Apa kepanjangan dari BTS dalam konteks jaringan seluler?', options: ['Base Technology System', 'Broadband Transmitting Structure', 'Base Transceiver Station', 'Basic Telephone Service'], correctAnswer: 'Base Transceiver Station', explanation: 'BTS adalah stasiun pemancar dan penerima yang menghubungkan perangkat seluler ke jaringan operator.' },
    { question: 'Manakah faktor berikut yang TIDAK secara langsung mempengaruhi kekuatan sinyal ponsel?', options: ['Jarak dari BTS', 'Material bangunan', 'Warna casing ponsel', 'Cuaca buruk'], correctAnswer: 'Warna casing ponsel', explanation: 'Warna casing ponsel tidak mempengaruhi penerimaan sinyal. Jarak, halangan fisik, dan cuaca adalah faktor penting.' },
];

const QUEST_4_QUIZ: QuizQuestion[] = [
    { question: 'Bagaimana data dikirimkan melalui jaringan seluler?', options: ['Sebagai satu blok besar', 'Dalam bentuk paket-paket kecil', 'Melalui gelombang radio analog saja', 'Hanya melalui kabel serat optik'], correctAnswer: 'Dalam bentuk paket-paket kecil', explanation: 'Data digital dipecah menjadi paket-paket kecil untuk transmisi yang efisien dan handal melalui jaringan.' },
    { question: 'Apa yang terjadi saat ponsel Anda "mengirim" data?', options: ['Ponsel memancarkan sinyal langsung ke satelit', 'Ponsel mengirim sinyal ke BTS terdekat', 'Data disimpan di cloud terlebih dahulu', 'BTS mengirim sinyal langsung ke ponsel tujuan'], correctAnswer: 'Ponsel mengirim sinyal ke BTS terdekat', explanation: 'Ponsel berkomunikasi dengan BTS terdekat, yang kemudian meneruskan data ke jaringan inti operator.' },
];

const QUEST_5_QUIZ: QuizQuestion[] = [
    { question: 'Apa tujuan utama dari serangan "web phishing"?', options: ['Merusak komputer korban', 'Mencuri informasi sensitif seperti kata sandi dan data kartu kredit', 'Membuat website menjadi lambat', 'Menyebarkan berita palsu'], correctAnswer: 'Mencuri informasi sensitif seperti kata sandi dan data kartu kredit', explanation: 'Phishing adalah upaya menipu korban untuk mengungkapkan informasi pribadi dengan menyamar sebagai entitas tepercaya.' },
    { question: 'Manakah ciri-ciri yang sering ditemukan pada email phishing?', options: ['Menggunakan alamat email resmi perusahaan', 'Tata bahasa sempurna dan profesional', 'Permintaan mendesak untuk data pribadi atau klik tautan mencurigakan', 'Tidak ada tautan sama sekali'], correctAnswer: 'Permintaan mendesak untuk data pribadi atau klik tautan mencurigakan', explanation: 'Email phishing seringkali menciptakan rasa urgensi, berisi kesalahan tata bahasa, dan mengarahkan ke situs palsu.' },
];

const QUEST_6_QUIZ: QuizQuestion[] = [
    { question: 'Mengapa penting untuk menggunakan koneksi HTTPS saat browsing?', options: ['Membuat website lebih cepat', 'Menandakan website memiliki desain bagus', 'Mengenkripsi data antara browser Anda dan server web, sehingga lebih aman', 'Menjamin tidak ada iklan'], correctAnswer: 'Mengenkripsi data antara browser Anda dan server web, sehingga lebih aman', explanation: 'HTTPS (HyperText Transfer Protocol Secure) mengenkripsi komunikasi, melindungi data dari penyadapan.' },
    { question: 'Manakah pengaturan browser yang dapat membantu meningkatkan keamanan online Anda?', options: ['Menonaktifkan semua cookie', 'Mengizinkan semua pop-up', 'Memperbarui browser secara teratur dan mengelola izin situs', 'Menyimpan semua kata sandi di browser tanpa proteksi master password'], correctAnswer: 'Memperbarui browser secara teratur dan mengelola izin situs', explanation: 'Browser yang diperbarui memiliki patch keamanan terbaru, dan mengelola izin situs membantu mengontrol akses data.' },
];


export const QUESTS: Quest[] = [
  {
    id: 'Q1',
    title: 'Misi 1: Koneksi Lokal Pertama',
    description: 'Bantu Teknisi Jaringan memperbaiki koneksi di Community Center yang rusak. Pelajari dasar-dasar Jaringan Lokal (LAN).',
    badgeName: 'Network Novice',
    icon: <CpuChipIcon className="w-8 h-8 text-blue-500" />,
    stages: [
      { id: 'Q1S1', type: StageType.INFO, title: 'Selamat Datang, Agen Digital!', infoText: ['Kamu adalah Agen Digital terpilih! Misi pertamamu adalah di Community Center. Jaringan lokal mereka mati total. Ayo bantu mereka!', 'Seorang teknisi bernama Pak Budi sudah menunggumu di sana.'], backgroundImageUrl: 'https://picsum.photos/seed/q1s1/800/600' },
      { id: 'Q1S2', type: StageType.DIALOGUE, npcName: 'Pak Budi', dialogue: ['Selamat datang, Agen! Saya Pak Budi. Aduh, pusing ini, semua komputer tidak bisa terhubung. Sepertinya ada masalah dengan kabel atau routernya.', 'Bisa bantu saya identifikasi komponen dan menghubungkannya dengan benar?'], backgroundImageUrl: 'https://picsum.photos/seed/q1s2/800/600' },
      { id: 'Q1S3', type: StageType.CHALLENGE, title: 'Tantangan: Rakit Jaringan!', challengeDescription: 'Identifikasi dan hubungkan komponen jaringan (Router, Switch, PC) dengan benar. Kumpulkan Kabel Jaringan.', challengeComponent: 'LAN_SETUP', pointsAwarded: 50, backgroundImageUrl: 'https://picsum.photos/seed/q1s3/800/600' },
      { id: 'Q1S4', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.NETWORK_CABLE, infoText: 'Kamu berhasil menghubungkan kabel! Item "Kabel Jaringan" ditambahkan ke inventaris.', pointsAwarded: 20 },
      { id: 'Q1S5', type: StageType.DIALOGUE, npcName: 'Pak Budi', dialogue: ['Hebat! Kabel sudah terpasang. Sekarang, kita perlu memastikan konfigurasinya benar. Router ini butuh Konfigurasi Router dan Skema IP yang tepat.'], backgroundImageUrl: 'https://picsum.photos/seed/q1s2/800/600' },
      { id: 'Q1S6', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.ROUTER_CONFIG, infoText: 'Kamu menemukan file Konfigurasi Router! Item ditambahkan.', pointsAwarded: 20 },
      { id: 'Q1S7', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.IP_SCHEMA, infoText: 'Skema IP Address ditemukan! Item ditambahkan.', pointsAwarded: 20 },
      { id: 'Q1S8', type: StageType.QUIZ, title: 'Uji Pengetahuan: Jaringan Lokal', quizQuestions: QUEST_1_QUIZ, backgroundImageUrl: 'https://picsum.photos/seed/q1s4/800/600' },
      { id: 'Q1S9', type: StageType.QUEST_COMPLETE, title: 'Misi 1 Selesai!', infoText: 'Kerja bagus, Agen! Jaringan di Community Center kembali pulih. Kamu mendapatkan lencana "Network Novice"!', pointsAwarded: 100 }
    ]
  },
  {
    id: 'Q2',
    title: 'Misi 2: Gerbang Menuju Dunia Maya',
    description: 'Koneksi internet kota terganggu! Selidiki cara kerja internet dan routing untuk memulihkan akses.',
    badgeName: 'Internet Navigator',
    icon: <GlobeAltIcon className="w-8 h-8 text-green-500" />,
    stages: [
      { id: 'Q2S1', type: StageType.INFO, title: 'Panggilan Darurat!', infoText: ['Agen, ada masalah besar! Seluruh kota kehilangan akses internet. Kamu harus mencari tahu penyebabnya dan memahami bagaimana data berjalan di internet.'], backgroundImageUrl: 'https://picsum.photos/seed/q2s1/800/600' },
      { id: 'Q2S2', type: StageType.DIALOGUE, npcName: 'Operator ISP', dialogue: ['Kami mendeteksi anomali pada jalur data utama. Sepertinya ada yang salah dengan pemahaman routing paket data.', 'Gunakan Pelacak Paket Data ini untuk melihat bagaimana data bergerak.'], backgroundImageUrl: 'https://picsum.photos/seed/q2s2/800/600' },
      { id: 'Q2S3', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.PACKET_TRACER, infoText: 'Pelacak Paket Data berhasil didapatkan! Sekarang kamu bisa melihat perjalanan datamu.', pointsAwarded: 20 },
      { id: 'Q2S4', type: StageType.CHALLENGE, title: 'Tantangan: Labirin Data!', challengeDescription: 'Gunakan Pelacak Paket Data untuk memandu paket data melewati router yang benar menuju tujuannya. Pelajari tentang peran DNS.', challengeComponent: 'ROUTING_SIM', pointsAwarded: 50, backgroundImageUrl: 'https://picsum.photos/seed/q2s3/800/600' },
      { id: 'Q2S5', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.DNS_ADDRESS, infoText: 'Kamu berhasil menemukan Alamat Server DNS yang benar!', pointsAwarded: 20 },
      { id: 'Q2S6', type: StageType.QUIZ, title: 'Uji Pengetahuan: Internet & Routing', quizQuestions: QUEST_2_QUIZ, backgroundImageUrl: 'https://picsum.photos/seed/q2s4/800/600' },
      { id: 'Q2S7', type: StageType.QUEST_COMPLETE, title: 'Misi 2 Selesai!', infoText: 'Internet kota kembali normal berkat keahlianmu! Kamu mendapatkan lencana "Internet Navigator"!', pointsAwarded: 100 }
    ]
  },
   {
    id: 'Q3',
    title: 'Misi 3: Misteri Sinyal Seluler',
    description: 'Sebuah pos penelitian terpencil kesulitan komunikasi seluler. Selidiki faktor-faktor yang mempengaruhi kekuatan sinyal.',
    badgeName: 'Signal Savvy',
    icon: <DevicePhoneMobileIcon className="w-8 h-8 text-sky-500" />,
    stages: [
      { id: 'Q3S1', type: StageType.INFO, title: 'Hilang Kontak!', infoText: ['Agen, kami menerima laporan dari pos penelitian di pegunungan. Sinyal seluler mereka sangat lemah. Tugasmu adalah menyelidiki dan memahami mengapa ini terjadi.'], backgroundImageUrl: 'https://picsum.photos/seed/q3s1/800/600' },
      { id: 'Q3S2', type: StageType.DIALOGUE, npcName: 'Peneliti Sarah', dialogue: ['Terima kasih sudah datang! Komunikasi kami sangat terganggu. Kami butuh pemahaman lebih baik tentang BTS dan faktor yang mempengaruhi sinyal.', 'Ini Pengukur Sinyal, mungkin bisa membantu.'], backgroundImageUrl: 'https://picsum.photos/seed/q3s2/800/600' },
      { id: 'Q3S3', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.SIGNAL_METER, infoText: 'Pengukur Sinyal ada di tanganmu. Saatnya mengukur!', pointsAwarded: 20 },
      { id: 'Q3S4', type: StageType.CHALLENGE, title: 'Tantangan: Detektif Sinyal!', challengeDescription: 'Gunakan Pengukur Sinyal dan informasi dari Peneliti Sarah untuk mengidentifikasi faktor-faktor (jarak BTS, halangan, cuaca) yang mempengaruhi sinyal di berbagai lokasi. Kumpulkan Cetak Biru BTS.', challengeComponent: 'SIGNAL_ANALYSIS', pointsAwarded: 50, backgroundImageUrl: 'https://picsum.photos/seed/q3s3/800/600' },
      { id: 'Q3S5', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.BTS_BLUEPRINT, infoText: 'Cetak Biru BTS berhasil ditemukan! Ini akan membantu memahami cara kerja BTS.', pointsAwarded: 20 },
      { id: 'Q3S6', type: StageType.QUIZ, title: 'Uji Pengetahuan: BTS & Kekuatan Sinyal', quizQuestions: QUEST_3_QUIZ, backgroundImageUrl: 'https://picsum.photos/seed/q3s4/800/600' },
      { id: 'Q3S7', type: StageType.QUEST_COMPLETE, title: 'Misi 3 Selesai!', infoText: 'Pemahamanmu tentang sinyal seluler sangat membantu! Pos penelitian kini bisa berkomunikasi lebih baik. Kamu mendapatkan lencana "Signal Savvy"!', pointsAwarded: 100 }
    ]
  },
  {
    id: 'Q4',
    title: 'Misi 4: Pengiriman Data Rahasia',
    description: 'Kirim pesan terenkripsi melalui jaringan seluler. Pelajari mekanisme pengiriman dan penerimaan data pada ponsel.',
    badgeName: 'Data Diplomat',
    icon: <ServerStackIcon className="w-8 h-8 text-purple-500" />,
    stages: [
      { id: 'Q4S1', type: StageType.INFO, title: 'Pesan Super Rahasia!', infoText: ['Agen, ada pesan penting yang harus dikirim dengan aman melalui jaringan seluler. Kamu harus memahami bagaimana data dikirim dan diterima oleh ponsel.'], backgroundImageUrl: 'https://picsum.photos/seed/q4s1/800/600' },
      { id: 'Q4S2', type: StageType.DIALOGUE, npcName: 'Agen Kripto', dialogue: ['Pesan ini sangat vital. Kita harus memastikan data terkirim sebagai paket-paket terenkripsi. Ini Chip Data Terenkripsi untuk menyimpan pesanmu setelah dienkripsi.'], backgroundImageUrl: 'https://picsum.photos/seed/q4s2/800/600' },
      { id: 'Q4S3', type: StageType.CHALLENGE, title: 'Tantangan: Enkripsi & Kirim!', challengeDescription: 'Pelajari cara data dipecah menjadi paket dan dikirim. Lakukan enkripsi sederhana pada pesan, lalu simulasikan pengirimannya melalui jaringan seluler (BTS ke jaringan inti).', challengeComponent: 'DATA_TRANSMISSION_SIM', pointsAwarded: 50, backgroundImageUrl: 'https://picsum.photos/seed/q4s3/800/600' },
      { id: 'Q4S4', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.ENCRYPTED_CHIP, infoText: 'Pesan berhasil dienkripsi dan disimpan dalam Chip Data Terenkripsi!', pointsAwarded: 20 },
      { id: 'Q4S5', type: StageType.QUIZ, title: 'Uji Pengetahuan: Komunikasi Data Ponsel', quizQuestions: QUEST_4_QUIZ, backgroundImageUrl: 'https://picsum.photos/seed/q4s4/800/600' },
      { id: 'Q4S6', type: StageType.QUEST_COMPLETE, title: 'Misi 4 Selesai!', infoText: 'Pesan rahasia terkirim dengan aman! Keahlianmu dalam transmisi data luar biasa. Kamu mendapatkan lencana "Data Diplomat"!', pointsAwarded: 100 }
    ]
  },
  {
    id: 'Q5',
    title: 'Misi 5: Ancaman Phishing',
    description: 'Warga kota menerima email mencurigakan. Identifikasi dan laporkan upaya phishing untuk melindungi mereka.',
    badgeName: 'Phishing Fighter',
    icon: <ShieldCheckIcon className="w-8 h-8 text-pink-500" />,
    stages: [
      { id: 'Q5S1', type: StageType.INFO, title: 'Waspada Penipuan!', infoText: ['Agen, banyak warga melaporkan email aneh yang meminta data pribadi. Ini pasti ulah phisher! Tugasmu adalah mengedukasi warga dan menghentikan mereka.'], backgroundImageUrl: 'https://picsum.photos/seed/q5s1/800/600' },
      { id: 'Q5S2', type: StageType.DIALOGUE, npcName: 'Kepala Keamanan Siber', dialogue: ['Kita harus bertindak cepat. Pelajari contoh-contoh email ini, identifikasi mana yang phishing, dan kumpulkan sebagai bukti.'], backgroundImageUrl: 'https://picsum.photos/seed/q5s2/800/600' },
      { id: 'Q5S3', type: StageType.CHALLENGE, title: 'Tantangan: Berburu Phisher!', challengeDescription: 'Analisis beberapa contoh email dan website. Tandai mana yang merupakan upaya phishing berdasarkan ciri-cirinya. Kumpulkan Bukti Phishing.', challengeComponent: 'PHISHING_DETECTION', pointsAwarded: 50, backgroundImageUrl: 'https://picsum.photos/seed/q5s3/800/600' },
      { id: 'Q5S4', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.PHISHING_EVIDENCE, infoText: 'Kamu berhasil mengumpulkan Bukti Phishing! Ini akan membantu investigasi.', pointsAwarded: 20 },
      { id: 'Q5S5', type: StageType.QUIZ, title: 'Uji Pengetahuan: Web Phishing', quizQuestions: QUEST_5_QUIZ, backgroundImageUrl: 'https://picsum.photos/seed/q5s4/800/600' },
      { id: 'Q5S6', type: StageType.QUEST_COMPLETE, title: 'Misi 5 Selesai!', infoText: 'Kamu telah melindungi banyak orang dari penipuan! Kamu mendapatkan lencana "Phishing Fighter"!', pointsAwarded: 100 }
    ]
  },
  {
    id: 'Q6',
    title: 'Misi 6: Benteng Digital Pribadi',
    description: 'Amankan browsermu dan ajari orang lain cara terhubung ke internet dengan aman. Ini adalah ujian terakhirmu!',
    badgeName: 'Cyber Guardian',
    icon: <ShieldCheckIcon className="w-8 h-8 text-teal-500" />,
    stages: [
      { id: 'Q6S1', type: StageType.INFO, title: 'Garis Pertahanan Terakhir!', infoText: ['Agen, misi terakhirmu adalah memastikan semua orang, termasuk dirimu, tahu cara menjelajah internet dengan aman. Fokus pada pengaturan keamanan browser.'], backgroundImageUrl: 'https://picsum.photos/seed/q6s1/800/600' },
      { id: 'Q6S2', type: StageType.DIALOGUE, npcName: 'Mentor Digital', dialogue: ['Keamanan dimulai dari diri sendiri. Mari kita periksa dan optimalkan pengaturan keamanan browser-mu. Setelah itu, kamu bisa mengajarkannya kepada orang lain.'], backgroundImageUrl: 'https://picsum.photos/seed/q6s2/800/600' },
      { id: 'Q6S3', type: StageType.CHALLENGE, title: 'Tantangan: Kunci Browser!', challengeDescription: 'Konfigurasikan pengaturan keamanan pada browser virtual (aktifkan HTTPS, blokir pop-up, kelola cookie). Dapatkan Sertifikat Keamanan Browser.', challengeComponent: 'BROWSER_SECURITY_SETUP', pointsAwarded: 50, backgroundImageUrl: 'https://picsum.photos/seed/q6s3/800/600' },
      { id: 'Q6S4', type: StageType.COLLECT_ITEM, itemToCollect: ITEMS.BROWSER_CERTIFICATE, infoText: 'Browser-mu kini aman! Sertifikat Keamanan Browser telah didapatkan.', pointsAwarded: 20 },
      { id: 'Q6S5', type: StageType.QUIZ, title: 'Uji Pengetahuan: Keamanan Browser', quizQuestions: QUEST_6_QUIZ, backgroundImageUrl: 'https://picsum.photos/seed/q6s4/800/600' },
      { id: 'Q6S6', type: StageType.QUEST_COMPLETE, title: 'Misi 6 Selesai!', infoText: 'Luar biasa, Agen! Kamu telah menguasai seni keamanan digital dan siap melindungi dunia maya! Kamu mendapatkan lencana "Cyber Guardian"!', pointsAwarded: 100 }
    ]
  }
];

export const ALL_BADGES = QUESTS.map(q => q.badgeName).concat(["Master of Networks"]);

export const INITIAL_PLAYER_STATE = {
  points: 0,
  inventory: [],
  badges: [],
};

// Placeholder for NPC data if needed more broadly
export const NPCS = {
  PAK_BUDI: { id: 'NPC1', name: 'Pak Budi', avatarUrl: 'https://picsum.photos/seed/budi/100/100' },
  OPERATOR_ISP: { id: 'NPC2', name: 'Operator ISP', avatarUrl: 'https://picsum.photos/seed/isp/100/100' },
  PENELITI_SARAH: { id: 'NPC3', name: 'Peneliti Sarah', avatarUrl: 'https://picsum.photos/seed/sarah/100/100' },
  AGEN_KRIPTO: { id: 'NPC4', name: 'Agen Kripto', avatarUrl: 'https://picsum.photos/seed/kripto/100/100' },
  KEPALA_KEAMANAN: { id: 'NPC5', name: 'Kepala Keamanan Siber', avatarUrl: 'https://picsum.photos/seed/cybersec/100/100' },
  MENTOR_DIGITAL: { id: 'NPC6', name: 'Mentor Digital', avatarUrl: 'https://picsum.photos/seed/mentor/100/100' },
};
    