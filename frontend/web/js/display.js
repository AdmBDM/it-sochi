function startDisplay(id) {
    async function fetchAndRender() {
        const res = await fetch(`/info/data?id=${id}`);
        const json = await res.json();
        const app = document.getElementById('app');
        app.innerHTML = '';

        json.blocks.forEach(block => {
            if (block.type === 'schedule') {
                let html = '<table border="1" cellpadding="5"><thead><tr><th>Мастер</th><th>Авто</th><th>Время</th><th>Статус</th></tr></thead><tbody>';
                block.data.forEach(r => {
                    html += `<tr>
            <td>${r.worker_name}</td>
            <td>${r.car_model} (${r.car_number || '—'})</td>
            <td>${r.start_repair} - ${r.finish_repair}</td>
            <td>${r.condition || '—'}</td>
          </tr>`;
                });
                html += '</tbody></table>';
                app.innerHTML = html;
            }
        });

        setTimeout(fetchAndRender, 60000); // обновление раз в минуту
    }

    fetchAndRender();
}
