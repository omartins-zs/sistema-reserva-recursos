import '@fortawesome/fontawesome-free/css/all.min.css';
import Swal from 'sweetalert2';

window.Swal = Swal;

document.addEventListener('livewire:init', () => {
    Livewire.on('notify', ({ type = 'info', title = '', text = '' }) => {
        Swal.fire({
            icon: type,
            title,
            text,
            confirmButtonColor: '#0f4c81',
            background: '#ffffff',
            color: '#0f172a',
        });
    });

    Livewire.on('scroll-top', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    });
});
