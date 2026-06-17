<header class="w-full p-2 flex justify-between items-center fixed top-0 bg-white shadow-md z-20">
    <!-- Ikon Home -->
    <div class="home-button w-20 h-20 cursor-pointer" onclick="handleHomeClick();">
        <img src="Uploads/home.png" alt="Beranda" class="w-full h-full object-contain">
    </div>
</header>

<!-- Tambahkan ini -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function handleHomeClick() {
    const isKuisPlay = window.location.pathname.includes("kuis-play.php");

    if (isKuisPlay) {
        Swal.fire({
            title: 'Keluar dari kuis?',
            text: "Skor kamu akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'kuis-exit.php';
            }
        });
    } else {
        window.location.href = 'main-menu.php';
    }
}
</script>

<style>
    .home-button {
        z-index: 50;
        position: relative;
    }
    header {
        max-height: 64px;
        overflow: hidden;
    }
</style>
