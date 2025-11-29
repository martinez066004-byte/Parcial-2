// Protección básica y colores automáticos de progreso
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => {
  if (!e.target.closest('input,textarea')) e.preventDefault();
});

function applyProgressColors(){
  document.querySelectorAll('.progress .progress-bar').forEach(pb=>{
    const w = parseInt(pb.style.width) || 0;
    pb.classList.remove('bg-danger','bg-warning','bg-success','bg-primary');
    if(w >= 100) pb.classList.add('bg-primary');
    else if(w >= 50) pb.classList.add('bg-success');
    else if(w >= 25) pb.classList.add('bg-warning');
    else pb.classList.add('bg-danger');
  });
}
document.addEventListener('DOMContentLoaded', applyProgressColors);