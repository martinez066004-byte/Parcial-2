document.addEventListener('DOMContentLoaded', function(){
  const sendBtn = document.getElementById('send');
  const input = document.getElementById('msg');
  if (sendBtn) sendBtn.addEventListener('click', sendMessage);
  if (input) input.addEventListener('keydown', e => { if(e.key === 'Enter') sendMessage(); });

  function normalize(text) {
    return (text || '').toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');
  }

  function addMsg(who, text){
    const box = document.getElementById('chatbox');
    const el = document.createElement('div');
    el.className = 'mb-2';
    el.innerHTML = `<b>${who}:</b> ${text}`;
    box.appendChild(el);
    box.scrollTop = box.scrollHeight;
  }

  function sendMessage(){
    const text = input.value.trim();
    if (!text) return;
    addMsg('Tú', text);
    input.value = '';
    setTimeout(() => addMsg('Bot', botReply(text)), 300);
  }

  function botReply(text){
    const q = normalize(text);

    if (q.match(/\b(hola|buenas|saludos|hey|que tal)\b/)) {
      return '¡Hola! 👋 Soy el asistente de NIBARRA. Estoy aquí para ayudarte a entender cómo usar el sistema.<br><br>Puedo responderte sobre:<ul><li>- El calendario de mantenimientos (calendario)</li><li>- Cómo registrar o editar equipos (equipo)</li><li>- Cómo funciona el progreso y los estados (progreso)</li><li>- Cómo funciona el mantenimiento (mantenimiento)</li></ul>';
    }

    if (q.includes('pregunta') || q.includes('duda') || q.includes('ayuda') || q.includes('como funciona')) {
      return 'Por supuesto 😊 dime tu duda. Puedo explicarte:<br>- Cómo registrar o modificar un equipo (equipo).<br>- Cómo se muestran los mantenimientos en el calendario (calendario).<br>- Qué significa el progreso (progreso).<br>- Qué significa el mantenimiento (mantenimiento).';
    }

    if (q.includes('calendario') || q.includes('fecha') || q.includes('horario')) {
      return 'El calendario muestra los mantenimientos de este mes. Cada equipo se representa con un punto amarillo en su ingreso o una línea roja desde el ingreso hasta la salida. Si el equipo ya está terminado, verás un punto verde en su fecha de salida.';
    }

    if (q.includes('equipo') || q.includes('registrar') || q.includes('nuevo') || q.includes('agregar')) {
      return 'Para registrar un nuevo equipo, entra a la pestaña "Equipos" y haz clic en "Nuevo". Podrás definir fechas, tipo de mantenimiento, estado y observaciones.';
    }

    if (q.includes('progreso') || q.includes('avance') || q.includes('porcentaje') || q.includes('estado')) {
      return 'El progreso muestra qué tan avanzado está un mantenimiento (0 a 100%).<br>Cuando guardas un cambio, el sistema puede sugerirte actualizar el estado si el porcentaje lo amerita.';
    }

    if (q.includes('mantenimiento') || q.includes('reparacion') || q.includes('servicio')) {
      return 'La sección "Mantenimiento" te muestra todos los equipos organizados por estado: Por hacer, En revisión, Espera material o Terminada.';
    }

    return 'Hmm 🤔 no estoy seguro de haber entendido. Puedes preguntarme sobre calendario, equipos, progreso o mantenimiento.';
  }
});