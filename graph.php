<?php
// admin/graph.php
// require_once __DIR__ . '/../auth.php';
// require_login();
require_once __DIR__ . '/db.php';
include __DIR__ . '../partials/header.php';
?>
<style>
/* Controls sidebar (left) */
#controlsPanel {
    position: fixed;
    top: 96px;           /* push below header — adjust if needed */
    left: 20px;
    z-index: 5000;
    width: 320px;
    transition: transform 0.22s ease, opacity 0.2s ease;
    transform-origin: left top;
}

/* collapsed state moves it away */
#controlsPanel.collapsed {
    transform: translateX(-292px);
    opacity: 0.95;
}

/* slim handle when collapsed */
#controlsHandle {
    position: fixed;
    top: 120px;
    left: 0;
    transform: translateX(-50%);
    width: 42px;
    height: 48px;
    display: none;
    border-radius: 8px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(6px);
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 9999;
    color: #d1d5db;
}


/* card visuals */
.ctrl-card {
    background: rgba(8,10,15,0.75);
    border: 1px solid rgba(255,255,255,0.04);
    backdrop-filter: blur(8px);
    padding: 14px;
    border-radius: 12px;
    color: #d1d5db;
    box-shadow: 0 8px 30px rgba(2,6,23,0.6);
}

.small-muted { color: #9CA3AF; font-size:12px; }
.btn { cursor:pointer; }
.tag { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.03); color:#E6EEF8; }
.top-list { max-height:160px; overflow:auto; margin-top:6px; }

/* responsive: hide fixed sidebar on very small screens */
@media (max-width: 900px) {
  #controlsPanel { transform: translateX(-292px); }
  #controlsPanel.open-mobile { transform: translateX(0); }
  #controlsHandle { left: 8px; }
}

/* small helper for headings inside control */
.ctrl-title { font-weight:700; color:#60A5FA; font-size:15px; }
.ctrl-section { margin-top:10px; }

/* ensure network area has left margin so content is not covered */
.network-wrap { margin-left: 0px; transition: margin-left 0.25s ease; }
.network-wrap.shifted { margin-left: 360px; } /* when controls open, shift content (optional) */

#controlsPanel.collapsed #collapseBtn {
    display: none !important;
}

#controlsPanel.collapsed ~ #controlsHandle {
    display: flex !important;
}

#controlsPanel:not(.collapsed) ~ #controlsHandle {
    display: none !important;
}

#collapseBtn {
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
}

</style>

<section class="max-w-7xl mx-auto px-6 py-8 relative">

  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-bold text-ht_blue">APT Knowledge Graph</h1>
      <p class="text-xs text-ht_muted mt-1">Interactive relationships: APT ↔ Malware ↔ Tools — advanced visualization & analytics.</p>
    </div>

    <div class="flex items-center gap-3">
      <input id="searchBox" type="text" placeholder="Search nodes (APT, malware, tool)..." class="bg-ht_bg border border-ht_border px-3 py-2 rounded text-xs w-64">
      <select id="modeCluster" class="bg-ht_bg border border-ht_border px-3 py-2 rounded text-xs">
        <option value="none">Cluster: None</option>
        <option value="country">Cluster: Country</option>
        <option value="malware">Cluster: Malware Families</option>
        <option value="tool">Cluster: Tools</option>
      </select>
      <select id="layoutMode" class="bg-ht_bg border border-ht_border px-3 py-2 rounded text-xs">
        <option value="force">Layout: Force</option>
        <option value="hierarchical">Layout: Hierarchical</option>
        <option value="circular">Layout: Circular</option>
      </select>
      <button id="exportPNG" class="px-3 py-2 bg-ht_blue text-white rounded text-xs">Save PNG</button>
      <button id="exportJSON" class="px-3 py-2 border border-ht_border rounded text-xs">Export JSON</button>
    </div>
  </div>

  <div class="bg-ht_bg2 border border-ht_border rounded-xl p-4 mb-4 relative">
    <div id="network" style="height:680px; width:100%;"></div>

    <!-- controls left sidebar (collapsible) -->
    <div id="controlsPanel" class="ctrl-card">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          <div class="ctrl-title">Controls</div>
          <div class="small-muted">Visual & physics</div>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
          <button id="resetView" class="btn tag">Reset</button>
          <button id="collapseBtn" class="btn" title="Collapse controls" style="background:transparent; border:none; color:#9CA3AF;">✕</button>
        </div>
      </div>

      <div class="ctrl-section">
        <label class="small-muted">Physics</label>
        <input id="physicsToggle" type="checkbox" checked style="margin-left:8px;">
      </div>

      <div class="ctrl-section">
        <label class="small-muted">Node size</label>
        <input id="nodeSize" type="range" min="8" max="36" value="18" style="width:100%;">
      </div>

      <div class="ctrl-section">
        <label class="small-muted">Edge width multiplier</label>
        <input id="edgeWidth" type="range" min="1" max="6" value="2" style="width:100%;">
      </div>

      <div class="ctrl-section">
        <label class="small-muted">Show</label>
        <div style="display:flex; gap:8px; margin-top:8px;">
          <button id="showAPTs" class="tag" data-active="1">APTs</button>
          <button id="showMalware" class="tag" data-active="1">Malware</button>
          <button id="showTools" class="tag" data-active="1">Tools</button>
        </div>
      </div>

      <div class="ctrl-section">
        <strong class="small-muted">Top connected APTs</strong>
        <div id="topConnected" class="top-list mt-2"></div>
      </div>

      <div class="ctrl-section">
        <button id="downloadCSV" class="px-3 py-2 bg-red-600/30 text-red-300 rounded text-xs w-full">Export nodes/edges CSV</button>
      </div>
    </div>

    <!-- small handle to open when collapsed (hidden by default because panel visible) -->
    <div id="controlsHandle" title="Open controls" style="display:none;">☰</div>

  </div>

  <div class="flex gap-4">
    <div class="bg-ht_bg2 border border-ht_border rounded-xl p-4 text-xs w-64">
      <div class="font-semibold text-ht_blue mb-2">Legend</div>
      <div class="space-y-2">
        <div><span class="inline-block w-3 h-3 bg-[#60A5FA] mr-2 align-middle"></span> APT</div>
        <div><span class="inline-block w-3 h-3 bg-[#FB7185] mr-2 align-middle"></span> Malware</div>
        <div><span class="inline-block w-3 h-3 bg-[#F97316] mr-2 align-middle"></span> Tool</div>
        <div class="mt-2 text-ht_muted">Tip: click an APT → open profile. Search autocompletes & zooms.</div>
      </div>
    </div>

    <div id="nodeDetails" class="bg-ht_bg2 border border-ht_border rounded-xl p-4 text-xs flex-1">
      <div class="font-semibold text-ht_blue mb-2">Node Details</div>
      <div id="detailContent" class="text-ht_muted">Select a node to view details.</div>
    </div>
  </div>

</section>

<!-- libraries -->
<script type="text/javascript" src="https://unpkg.com/vis-network@9.1.2/dist/vis-network.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
(async function(){
  // load graph data
  const resp = await fetch('graph_data.php');
  const graph = await resp.json();

  // convert to vis nodes & edges with meta
  const rawNodes = graph.nodes || [];
  const rawEdges = graph.edges || [];

  // prepare lookup maps
  const nodesArr = rawNodes.map(n => {
    const isAPT = n.type === 'apt';
    return {
      id: n.id,
      label: n.label,
      title: `<b>${escapeHtml(n.label)}</b><br>Type: ${n.type}` + (isAPT ? `<br>Country: ${escapeHtml(n.country||'')}<br>Risk: ${n.risk||'-'}` : ''),
      type: n.type,
      meta: n,
      color: colorByTypeAndRisk(n)
    };
  });

  const edgesArr = rawEdges.map((e, i) => {
    const w = e.weight ? Math.max(1, e.weight*4) : 1.2;
    return {
      id: 'e'+i,
      from: e.from,
      to: e.to,
      label: e.label || '',
      width: w,
      dashes: e.type === 'overlap'
    };
  });

  // data sets
  const data = {
    nodes: new vis.DataSet(nodesArr.map(n => ({ id:n.id, label:n.label, title:n.title, color:n.color, size:(n.type==='apt'?22:14), type:n.type, meta:n.meta }))),
    edges: new vis.DataSet(edgesArr)
  };

  // network options
  let options = defaultOptions();
  let network = new vis.Network(document.getElementById('network'), data, options);

  // utilities - compute degree and top connected APTs
  function computeDegrees() {
    const degMap = {};
    data.nodes.get().forEach(n => degMap[n.id]=0);
    data.edges.get().forEach(e => { degMap[e.from] = (degMap[e.from]||0)+1; degMap[e.to] = (degMap[e.to]||0)+1; });
    const aptDegrees = data.nodes.get().filter(n=>n.type==='apt').map(n => ({ id:n.id, label:n.label, deg:degMap[n.id]||0 }));
    aptDegrees.sort((a,b)=>b.deg-a.deg);
    return aptDegrees;
  }

  function renderTopConnected() {
    const top = computeDegrees().slice(0,6);
    const el = document.getElementById('topConnected');
    el.innerHTML = '';
    top.forEach(t => {
      const row = document.createElement('div');
      row.style.display = 'flex'; row.style.justifyContent='space-between'; row.style.padding='6px 0';
      row.innerHTML = `<div style="cursor:pointer;color:#fff" onclick="zoomTo('${t.id}')">${escapeHtml(t.label)}</div><div class="small-muted">${t.deg}</div>`;
      el.appendChild(row);
    });
  }
  window.zoomTo = function(id) {
    network.focus(id, {scale:1.6, animation:{duration:500}});
  }

  renderTopConnected();

  // autocomplete source
  const searchEl = document.getElementById('searchBox');
  const allLabels = data.nodes.get().map(n => ({id:n.id,label:n.label.toLowerCase(), type:n.type}));
  let acTimeout;
  searchEl.addEventListener('input', e => {
    clearTimeout(acTimeout);
    acTimeout = setTimeout(()=> {
      const q = e.target.value.trim().toLowerCase();
      if (!q) { data.nodes.forEach(n => data.nodes.update({id:n.id, hidden:false})); return; }
      // highlight matches & zoom to first
      const matches = allLabels.filter(x=>x.label.includes(q));
      const matchIds = matches.map(m=>m.id);
      data.nodes.forEach(n => data.nodes.update({id:n.id, hidden: !matchIds.includes(n.id)}));
      if (matchIds.length) network.focus(matchIds[0], {scale:1.4, animation:{duration:400}});
    }, 180);
  });

  // click -> show details & open apt profile
  network.on('click', function(params) {
    if (params.nodes.length === 1) {
      const nid = params.nodes[0];
      const node = data.nodes.get(nid);
      showDetails(node);
      if (node.type === 'apt' && node.meta && node.meta.apt_id) {
        // window.open('/apt.php?id=' + encodeURIComponent(node.meta.apt_id), '_blank');
      }
    } else if (params.nodes.length > 1) {
      document.getElementById('detailContent').innerHTML = 'Multiple nodes selected.';
    } else {
      document.getElementById('detailContent').innerHTML = 'Select a node to view details.';
    }
  });

  // hover neighbor highlight
  network.on('hoverNode', function(params) { highlightNeighbors(params.node); });
  network.on('blurNode', function() { clearHighlight(); });

  function highlightNeighbors(nodeId) {
    const connected = network.getConnectedNodes(nodeId);
    data.nodes.forEach(n => {
      if (n.id === nodeId || connected.includes(n.id)) data.nodes.update({id:n.id, hidden:false});
      else data.nodes.update({id:n.id, color:'rgba(255,255,255,0.06)'});
    });
  }
  function clearHighlight() { data.nodes.forEach(n => data.nodes.update({id:n.id, color: colorByTypeAndRisk(n.meta || n)})); }

  // controls & bindings
  document.getElementById('modeCluster').addEventListener('change', (e)=> applyClusterMode(e.target.value));
  document.getElementById('layoutMode').addEventListener('change', (e)=> switchLayout(e.target.value));
  document.getElementById('physicsToggle').addEventListener('change', (e)=> togglePhysics(e.target.checked));
  document.getElementById('nodeSize').addEventListener('input', (e)=> adjustNodeSize(e.target.value));
  document.getElementById('edgeWidth').addEventListener('input', (e)=> adjustEdgeWidth(e.target.value));
  document.getElementById('showAPTs').addEventListener('click', ()=> toggleTypeButton('showAPTs','apt'));
  document.getElementById('showMalware').addEventListener('click', ()=> toggleTypeButton('showMalware','malware'));
  document.getElementById('showTools').addEventListener('click', ()=> toggleTypeButton('showTools','tool'));
  document.getElementById('resetView').addEventListener('click', ()=> resetView());
  document.getElementById('exportPNG').addEventListener('click', ()=> exportPNG());
  document.getElementById('exportJSON').addEventListener('click', ()=> exportJSON());
  document.getElementById('downloadCSV').addEventListener('click', ()=> exportCSV());

  // collapse behavior
  const controls = document.getElementById('controlsPanel');
  const handle = document.getElementById('controlsHandle');
  const collapseBtn = document.getElementById('collapseBtn');

  collapseBtn.addEventListener('click', () => {
    controls.classList.add('collapsed');
    handle.style.display = 'flex';
    // optional: shift network content slightly
    document.querySelector('.network-wrap')?.classList.remove('shifted');
  });
  handle.addEventListener('click', () => {
    controls.classList.remove('collapsed');
    handle.style.display = 'none';
    document.querySelector('.network-wrap')?.classList.add('shifted');
  });

  // when viewport small, start collapsed
  if (window.innerWidth <= 900) {
    controls.classList.add('collapsed');
    handle.style.display = 'flex';
  }

  let typeVisibility = { apt:true, malware:true, tool:true };

  function toggleTypeButton(btnId, type) {
    const btn = document.getElementById(btnId);
    const active = btn.getAttribute('data-active') === '1';
    btn.setAttribute('data-active', active ? '0' : '1');
    btn.style.opacity = active ? '0.5' : '1';
    typeVisibility[type] = !active;
    applyTypeFilters();
  }

  function applyTypeFilters() {
    data.nodes.forEach(n => {
      const t = n.type;
      // show if type visibility true, hide if false
      if (t === 'apt' && !typeVisibility.apt) { data.nodes.update({id:n.id, hidden:true}); return; }
      if (t === 'malware' && !typeVisibility.malware) { data.nodes.update({id:n.id, hidden:true}); return; }
      if (t === 'tool' && !typeVisibility.tool) { data.nodes.update({id:n.id, hidden:true}); return; }
      // otherwise visible
      data.nodes.update({id:n.id, hidden:false});
    });
  }

  function applyClusterMode(mode) {
    if (mode === 'none') { network.setOptions(defaultOptions()); return; }
    if (mode === 'country') {
      const aptNodes = data.nodes.get().filter(n => n.type==='apt');
      aptNodes.forEach((n, idx) => {
        const country = (n.meta && n.meta.country) ? n.meta.country : 'ZZ';
        const h = simpleHash(country) % 360;
        data.nodes.update({id:n.id, color: hslToHex(h,70,55)});
      });
      network.setOptions(Object.assign(defaultOptions(), {physics:{barnesHut:{gravitationalConstant:-12000}}}));
    } else if (mode === 'malware' || mode === 'tool') {
      try { network.clusterOutliers(); } catch(e){}
      const groups = {};
      data.nodes.forEach(n => {
        if (n.type === mode) {
          const key = n.label.charAt(0).toUpperCase();
          groups[key] = groups[key] || [];
          groups[key].push(n.id);
        }
      });
      Object.keys(groups).forEach(k => {
        const members = groups[k];
        if (members.length > 3) {
          network.cluster({ joinCondition: function(childOptions) {
            return members.indexOf(childOptions.id) !== -1;
          }, clusterNodeProperties: {id:'cluster_'+k, label: k + ' (' + members.length + ')', borderWidth:2, color:'#444' }});
        }
      });
    }
  }

  function switchLayout(mode) {
    if (mode === 'force') { network.setOptions(defaultOptions()); }
    else if (mode === 'hierarchical') {
      network.setOptions(Object.assign({}, defaultOptions(), {
        layout: { hierarchical: { enabled: true, direction: 'UD', sortMethod: 'directed' } },
        physics: { enabled: false }
      }));
    } else if (mode === 'circular') {
      const all = data.nodes.get();
      const centerX = 0, centerY = 0;
      const radius = 300;
      all.forEach((n, idx) => {
        const angle = (idx / all.length) * (Math.PI*2);
        data.nodes.update({id:n.id, x: Math.round(centerX + radius * Math.cos(angle)), y: Math.round(centerY + radius * Math.sin(angle)), fixed: {x:true, y:true}});
      });
      network.fit();
    }
  }

  function togglePhysics(enabled) { network.setOptions({ physics: { enabled: !!enabled } }); }
  function adjustNodeSize(size) { data.nodes.forEach(n => data.nodes.update({id:n.id, size: n.type==='apt' ? Math.max(12, size) : Math.max(10, size-4) })); }
  function adjustEdgeWidth(mul) { data.edges.forEach(e => { const base = e.width || 1; data.edges.update({id:e.id, width: Math.max(0.5, base * (mul/2))}); }); }

  function resetView() {
    data.nodes.forEach(n => data.nodes.update({id:n.id, fixed: false}));
    network.setOptions(defaultOptions());
    network.fit();
    data.nodes.forEach(n => data.nodes.update({id:n.id, color: colorByTypeAndRisk(n.meta || n)}));
    renderTopConnected();
  }

  function showDetails(node) {
    if (!node) return;
    let html = `<div style="font-weight:600;color:#fff">${escapeHtml(node.label)}</div>`;
    html += `<div class="small-muted">Type: ${node.type}</div>`;
    if (node.type === 'apt') {
      const m = node.meta || {};
      html += `<div style="margin-top:8px"><strong class="small-muted">Country</strong><div style="color:#fff">${escapeHtml(m.country||'-')}</div></div>`;
      html += `<div style="margin-top:6px"><strong class="small-muted">Risk</strong><div style="color:${riskColor(m.risk)}">${escapeHtml(m.risk||'-')}</div></div>`;
      if (m.aliases) html += `<div style="margin-top:6px"><strong class="small-muted">Aliases</strong><div>${escapeHtml(m.aliases)}</div></div>`;
      if (m.targeted_industries) html += `<div style="margin-top:6px"><strong class="small-muted">Targets</strong><div>${escapeHtml(m.targeted_industries)}</div></div>`;
      if (m.active_from || m.active_to) html += `<div style="margin-top:6px" class="small-muted">Active: ${escapeHtml(m.active_from||'-')} – ${escapeHtml(m.active_to||'Present')}</div>`;
      const neigh = network.getConnectedNodes(node.id);
      const mal = neigh.map(id=>data.nodes.get(id)).filter(x=>x && x.type==='malware').map(x=>x.label);
      const tools = neigh.map(id=>data.nodes.get(id)).filter(x=>x && x.type==='tool').map(x=>x.label);
      if (mal.length) html += `<div style="margin-top:6px"><strong class="small-muted">Malware</strong><div>${escapeHtml(mal.join(', '))}</div></div>`;
      if (tools.length) html += `<div style="margin-top:6px"><strong class="small-muted">Tools</strong><div>${escapeHtml(tools.join(', '))}</div></div>`;
    } else {
      const neigh = network.getConnectedNodes(node.id);
      const apts = neigh.map(id=>data.nodes.get(id)).filter(x=>x && x.type==='apt').map(x=>x.label);
      if (apts.length) html += `<div style="margin-top:6px"><strong class="small-muted">Used By</strong><div>${escapeHtml(apts.join(', '))}</div></div>`;
    }
    document.getElementById('detailContent').innerHTML = html;
  }

  async function exportPNG() {
    const el = document.getElementById('network');
    const bg = el.style.backgroundColor;
    try {
      el.style.backgroundColor = '#0b1220';
      const canvas = await html2canvas(el, { scale: 2, backgroundColor: null });
      const url = canvas.toDataURL('image/png');
      const a = document.createElement('a');
      a.href = url;
      a.download = 'intelctx_graph.png';
      document.body.appendChild(a);
      a.click();
      a.remove();
    } catch (e) {
      alert('Export failed: ' + e.message);
    } finally {
      el.style.backgroundColor = bg;
    }
  }

  function exportJSON() {
    const payload = { nodes: data.nodes.get(), edges: data.edges.get() };
    const blob = new Blob([JSON.stringify(payload, null, 2)], {type:'application/json'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'graph.json';
    document.body.appendChild(a); a.click(); a.remove();
  }

  function exportCSV() {
    const nodesCsv = ['id,label,type,meta_country,meta_risk'];
    data.nodes.get().forEach(n => {
      const m = n.meta || {};
      nodesCsv.push([n.id, `"${n.label.replace(/"/g,'""')}"`, n.type, `"${(m.country||'').replace(/"/g,'""')}"`, m.risk||''].join(','));
    });
    const edgesCsv = ['from,to,label,width,type'];
    data.edges.get().forEach(e => edgesCsv.push([e.from, e.to, `"${(e.label||'').replace(/"/g,'""')}"`, e.width||'', e.dashes? 'overlap':'uses'].join(',')));
    const blob = new Blob([nodesCsv.join("\n") + "\n\n" + edgesCsv.join("\n")], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'graph_nodes_edges.csv';
    document.body.appendChild(a); a.click(); a.remove();
  }

  // helper color functions
  function colorByTypeAndRisk(n) {
    const t = n.type || n.type;
    if (t === 'apt') return riskColor(n.risk || (n.meta && n.meta.risk) || 0);
    if (t === 'malware') return '#FB7185';
    if (t === 'tool') return '#F97316';
    return '#9CA3AF';
  }
  function riskColor(risk) {
    risk = Number(risk) || 0;
    if (risk >= 8) return '#ef4444';
    if (risk >= 6) return '#f59e0b';
    if (risk >= 4) return '#f97316';
    return '#10b981';
  }

  function simpleHash(s) {
    let h = 0; for (let i=0;i<s.length;i++) h = (h<<5)-h + s.charCodeAt(i); return Math.abs(h);
  }
  function hslToHex(h,s,l) {
    s/=100; l/=100;
    const k = n => (n + h/30) % 12;
    const a = s * Math.min(l,1-l);
    const f = n => Math.round(255*(l - a * Math.max(Math.min(k(n)-3,9-k(n),1),-1)));
    return '#'+[f(0),f(8),f(4)].map(x=>x.toString(16).padStart(2,'0')).join('');
  }

  function defaultOptions() {
    return {
      physics: { enabled: true, stabilization: { iterations: 250 }, barnesHut: { gravitationalConstant: -20000, springConstant: 0.001, springLength: 200 } },
      interaction: { hover: true, multiselect: true, tooltipDelay: 150 },
      nodes: { shape: 'dot', font: { size: 12, color: '#fff' } },
      edges: { color: 'rgba(255,255,255,0.08)', smooth: { type: 'forceAtlas2Based' } }
    };
  }

  function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/[&<>"'`=\/]/g, function (ch) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#47;','`':'&#96;','=':'&#61;'}[ch];
    });
  }

  // initial post-processing
  data.nodes.forEach(n => {
    const raw = rawNodes.find(r => r.id === n.id);
    if (raw) data.nodes.update({ id: n.id, meta: raw });
  });

  renderTopConnected();

  let simMode = false;
  document.addEventListener('keydown', (e)=> { if (e.key === 's') { simMode = !simMode; applySimMode(simMode); } });
  function applySimMode(on) {
    if (!on) { data.edges.forEach(e => data.edges.update({id:e.id, hidden:false})); return; }
    data.edges.forEach(e => {
      if ((e.label || '').toLowerCase() === 'overlap') data.edges.update({id:e.id, width: Math.max(2, (e.width||1)*3), color:'#7c3aed'});
      else data.edges.update({id:e.id, hidden:true});
    });
  }

})();
</script>

</main>
<?php include __DIR__ . '../partials/footer.php'; ?>
