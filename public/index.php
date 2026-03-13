<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detect Collisions — PHP 8 OOP + React + Canvas</title>
  <link rel="preconnect" href="https://unpkg.com">
  <style>
    :root {
      --bg: #0b0b0b;
      --panel: #121212;
      --fg: #e9e9e9;
      --muted: #a0a0a0;
      --acc: #7aa2f7;
      --ok: #46c36f;
      --bad: #ff5757;
    }

    html,
    body {
      height: 100%;
    }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--fg);
      font: 14px/1.4 system-ui, Segoe UI, Roboto, Arial;
    }

    .wrap {
      display: grid;
      grid-template-columns: 460px 1fr;
      gap: 16px;
      height: 100vh;
    }

    header {
      grid-column: 1/-1;
      padding: 12px 16px;
      background: #151515;
      border-bottom: 1px solid #222;
    }

    h1 {
      margin: 0;
      font-size: 18px;
    }

    .panel {
      padding: 16px;
      overflow: auto;
    }

    .card {
      background: var(--panel);
      border: 1px solid #222;
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 12px;
    }

    label {
      display: block;
      margin: 8px 0 4px;
      color: var(--muted);
    }

    input[type="number"] {
      width: 100%;
      padding: 8px;
      border-radius: 8px;
      border: 1px solid #333;
      background: #0e0e0e;
      color: var(--fg);
    }

    select,
    button {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #333;
      background: #1a1a1a;
      color: var(--fg);
    }

    button.primary {
      background: var(--acc);
      border-color: var(--acc);
      color: #00121f;
      font-weight: 600;
    }

    .grid-2 label {
      font-size: 13px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .grid-2 input[type="number"] {
      width: 95%;
      box-sizing: border-box;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }

    .grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }

    .good {
      color: var(--ok);
      font-weight: 700;
    }

    .bad {
      color: var(--bad);
      font-weight: 700;
    }

    canvas {
      display: block;
      width: 100%;
      height: 100%;
      max-width: 100%;
      max-height: 100%;
      background: #0f0f0f;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .canvas-wrap {
      height: calc(100vh - 120px);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .legend {
      background: var(--panel);
      border: 1px solid #222;
      border-top: none;
      padding: 8px 12px;
      border-bottom-left-radius: 12px;
      border-bottom-right-radius: 12px;
      display: flex;
      gap: 16px;
    }

    .chip {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--muted);
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #777;
    }

    .dot.a {
      background: #7aa2f7;
    }

    .dot.b {
      background: #a6da95;
    }

    .dot.grid {
      background: #ffb86c;
    }

    .cols {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }

    .subtle {
      color: var(--muted);
      font-size: 12px;
    }
  </style>
</head>

<body>
  <div class="wrap">
    <header>
      <h1>Detect Collisions — PHP 8 OOP + React + Canvas</h1>
    </header>
    <section class="panel">
      <div id="root"></div>
    </section>
    <section class="panel canvas-wrap">
      <canvas id="stage"></canvas>
      <div class="legend">
        <div class="chip"><span class="dot a"></span>Obiekt A</div>
        <div class="chip"><span class="dot b"></span>Obiekt B</div>
        <div class="chip"><span class="dot grid"></span>Siatka / osie</div>
      </div>
    </section>
  </div>

  <script type="module">
    import React, {
      useEffect,
      useRef,
      useState
    } from "https://unpkg.com/es-react/dev/react.js";
    import ReactDOM from "https://unpkg.com/es-react/dev/react-dom.js";

    class Visualizer {
      constructor(canvas) {
        this.c = canvas;
        this.ctx = canvas.getContext('2d');
        this.center = {
          x: canvas.width / 2,
          y: canvas.height / 2
        };
        this.scale = 1;
      }
      clear() {
        this.ctx.clearRect(0, 0, this.c.width, this.c.height);
      }
      worldToCanvas(x, y) {
        return {
          x: this.center.x + x * this.scale,
          y: this.center.y - y * this.scale
        };
      }
      drawAxes() {
        const g = this.ctx;
        g.save();
        g.strokeStyle = '#ffb86c';
        g.lineWidth = 1;
        g.beginPath();
        g.moveTo(0, this.center.y);
        g.lineTo(this.c.width, this.center.y);
        g.stroke();
        g.beginPath();
        g.moveTo(this.center.x, 0);
        g.lineTo(this.center.x, this.c.height);
        g.stroke();
        g.globalAlpha = 0.35;
        const step = 50;
        for (let x = this.center.x % step; x < this.c.width; x += step) {
          g.beginPath();
          g.moveTo(x, 0);
          g.lineTo(x, this.c.height);
          g.stroke();
        }
        for (let y = this.center.y % step; y < this.c.height; y += step) {
          g.beginPath();
          g.moveTo(0, y);
          g.lineTo(this.c.width, y);
          g.stroke();
        }
        g.restore();
      }
      drawPoint(x, y, color = '#7aa2f7', label = '') {
        const p = this.worldToCanvas(x, y);
        const g = this.ctx;
        g.save();
        g.fillStyle = color;
        g.beginPath();
        g.arc(p.x, p.y, 4, 0, Math.PI * 2);
        g.fill();
        if (label) {
          g.fillStyle = '#ddd';
          g.font = '12px system-ui';
          g.fillText(label, p.x + 6, p.y - 6);
        }
        g.restore();
      }
      drawCircle(cx, cy, r, color = '#7aa2f7', label = '') {
        const c = this.worldToCanvas(cx, cy);
        const g = this.ctx;
        g.save();
        g.strokeStyle = color;
        g.lineWidth = 2;
        g.beginPath();
        g.arc(c.x, c.y, r * this.scale, 0, Math.PI * 2);
        g.stroke();
        if (label) {
          g.fillStyle = '#ddd';
          g.font = '12px system-ui';
          g.fillText(label, c.x + 6, c.y - 6);
        }
        g.restore();
      }
      drawRect(x, y, w, h, color = '#a6da95', label = '') {
        // (x, y) to dolny lewy róg w układzie matematycznym
        const topLeft = this.worldToCanvas(x, y + h);
        const g = this.ctx;
        g.save();
        g.strokeStyle = color;
        g.lineWidth = 2;

        // poprawione: wysokość z dodatnim znakiem (bo worldToCanvas już odwraca oś Y)
        g.strokeRect(topLeft.x, topLeft.y, w * this.scale, h * this.scale);

        if (label) {
          g.fillStyle = '#ddd';
          g.font = '12px system-ui';
          g.fillText(label, topLeft.x + 6, topLeft.y - 6);
        }
        g.restore();
      }

      drawTriangle(x1, y1, x2, y2, x3, y3, color = '#7aa2f7', label = '') {
        const p1 = this.worldToCanvas(x1, y1),
          p2 = this.worldToCanvas(x2, y2),
          p3 = this.worldToCanvas(x3, y3);
        const g = this.ctx;
        g.save();
        g.strokeStyle = color;
        g.lineWidth = 2;
        g.beginPath();
        g.moveTo(p1.x, p1.y);
        g.lineTo(p2.x, p2.y);
        g.lineTo(p3.x, p3.y);
        g.closePath();
        g.stroke();
        if (label) {
          g.fillStyle = '#ddd';
          g.font = '12px system-ui';
          g.fillText(label, p1.x + 6, p1.y - 6);
        }
        g.restore();
      }
      banner(text, ok) {
        const g = this.ctx;
        g.save();
        g.fillStyle = ok ? 'rgba(70,195,111,0.15)' : 'rgba(255,87,87,0.15)';
        g.fillRect(0, 0, this.c.width, 36);
        g.fillStyle = ok ? '#46c36f' : '#ff5757';
        g.font = 'bold 14px system-ui';
        g.fillText(text, 12, 22);
        g.restore();
      }
    }

    // FORM SCHEMA — dokładnie inne pola dla każdej pary figur
    const SCHEMA = {
      'point-point': {
        a: {
          title: 'Punkt A',
          fields: [
            ['x1', 'x'],
            ['y1', 'y']
          ]
        },
        b: {
          title: 'Punkt B',
          fields: [
            ['x2', 'x'],
            ['y2', 'y']
          ]
        }
      },
      'point-circle': {
        a: {
          title: 'Punkt P',
          fields: [
            ['px', 'x'],
            ['py', 'y']
          ]
        },
        b: {
          title: 'Koło C',
          fields: [
            ['cx', 'środek x'],
            ['cy', 'środek y'],
            ['r', 'promień']
          ]
        }
      },
      'circle-circle': {
        a: {
          title: 'Koło C1',
          fields: [
            ['c1x', 'środek x'],
            ['c1y', 'środek y'],
            ['r1', 'promień']
          ]
        },
        b: {
          title: 'Koło C2',
          fields: [
            ['c2x', 'środek x'],
            ['c2y', 'środek y'],
            ['r2', 'promień']
          ]
        }
      },
      'point-rect': {
        a: {
          title: 'Punkt P',
          fields: [
            ['px', 'x'],
            ['py', 'y']
          ]
        },
        b: {
          title: 'Prostokąt R (LB corner)',
          fields: [
            ['rx', 'x'],
            ['ry', 'y'],
            ['rw', 'szerokość'],
            ['rh', 'wysokość']
          ]
        }
      },
      'rect-rect': {
        a: {
          title: 'Prostokąt R1 (LB corner)',
          fields: [
            ['r1x', 'x'],
            ['r1y', 'y'],
            ['r1w', 'szerokość'],
            ['r1h', 'wysokość']
          ]
        },
        b: {
          title: 'Prostokąt R2 (LB corner)',
          fields: [
            ['r2x', 'x'],
            ['r2y', 'y'],
            ['r2w', 'szerokość'],
            ['r2h', 'wysokość']
          ]
        }
      },
      'circle-rect': {
        a: {
          title: 'Koło C',
          fields: [
            ['cx', 'środek x'],
            ['cy', 'środek y'],
            ['r', 'promień']
          ]
        },
        b: {
          title: 'Prostokąt R (LB corner)',
          fields: [
            ['rx', 'x'],
            ['ry', 'y'],
            ['rw', 'szerokość'],
            ['rh', 'wysokość']
          ]
        }
      },
      'triangle-point': {
        a: {
          title: 'Trójkąt T',
          fields: [
            ['x1', 'x1'],
            ['y1', 'y1'],
            ['x2', 'x2'],
            ['y2', 'y2'],
            ['x3', 'x3'],
            ['y3', 'y3']
          ]
        },
        b: {
          title: 'Punkt P',
          fields: [
            ['px', 'x'],
            ['py', 'y']
          ]
        }
      },
      'triangle-circle': {
        a: {
          title: 'Trójkąt T',
          fields: [
            ['x1', 'x1'],
            ['y1', 'y1'],
            ['x2', 'x2'],
            ['y2', 'y2'],
            ['x3', 'x3'],
            ['y3', 'y3']
          ]
        },
        b: {
          title: 'Koło C',
          fields: [
            ['cx', 'środek x'],
            ['cy', 'środek y'],
            ['r', 'promień']
          ]
        }
      }
    };

    function NumberField({
      name,
      label,
      value,
      onChange
    }) {
      return React.createElement('div', {}, [
        React.createElement('label', {
          htmlFor: name
        }, label),
        React.createElement('input', {
          type: 'number',
          step: 'any',
          id: name,
          name,
          value,
          onChange: e => onChange(name, e.target.value)
        })
      ]);
    }

    function Box({
      title,
      children
    }) {
      return React.createElement('div', {
        className: 'card'
      }, [
        React.createElement('div', {
          style: {
            fontWeight: 700,
            marginBottom: '8px'
          }
        }, title),
        children
      ]);
    }

    function SectionFields({
      prefix,
      schema,
      values,
      setField
    }) {
      const groups = [];
      // render fields in 2-column grid
      const items = schema.fields.map(([name, label]) => React.createElement(NumberField, {
        key: name,
        name,
        label,
        value: values[name] ?? '',
        onChange: setField
      }));
      return React.createElement('div', {
        className: 'grid-2'
      }, items);
    }

    function App() {
      const [caseVal, setCaseVal] = useState('circle-circle');
      const [values, setValues] = useState({
        c1x: -150,
        c1y: 0,
        r1: 140,
        c2x: 50,
        c2y: 0,
        r2: 120
      });
      const [result, setResult] = useState(null);
      const vizRef = useRef(null);

      useEffect(() => {
        const canvas = document.getElementById('stage');
        // dopasowuje rzeczywisty rozmiar canvasa do widocznego rozmiaru CSS
        canvas.width = canvas.clientWidth;
        canvas.height = canvas.clientHeight;
        vizRef.current = new Visualizer(canvas);
        draw();
      }, []);
      useEffect(() => {
        draw(result);
      }, [result]);

      const setField = (n, v) => setValues(prev => ({
        ...prev,
        [n]: v
      }));

      const schema = SCHEMA[caseVal];

      function applyPreset() {
        const presets = {
          'point-point': {
            x1: 0,
            y1: 0,
            x2: 50,
            y2: 0
          },
          'point-circle': {
            px: 0,
            py: 0,
            cx: 80,
            cy: 0,
            r: 60
          },
          'circle-circle': {
            c1x: -150,
            c1y: 0,
            r1: 140,
            c2x: 50,
            c2y: 0,
            r2: 120
          },
          'point-rect': {
            px: 10,
            py: 10,
            rx: 0,
            ry: 0,
            rw: 120,
            rh: 90
          },
          'rect-rect': {
            r1x: 0,
            r1y: 0,
            r1w: 240,
            r1h: 140,
            r2x: 120,
            r2y: 80,
            r2w: 140,
            r2h: 100
          },
          'circle-rect': {
            cx: -80,
            cy: 10,
            r: 90,
            rx: 0,
            ry: 0,
            rw: 220,
            rh: 120
          },
          'triangle-point': {
            x1: 0,
            y1: 0,
            x2: 120,
            y2: 0,
            x3: 0,
            y3: 120,
            px: 50,
            py: 40
          },
          'triangle-circle': {
            x1: 0,
            y1: 0,
            x2: 160,
            y2: 0,
            x3: 0,
            y3: 160,
            cx: 60,
            cy: 40,
            r: 30
          }
        };
        setValues(presets[caseVal]);
        setResult(null);
      }

      async function submit() {
        // validation: ensure required numbers are present
        const missing = [...schema.a.fields, ...schema.b.fields]
          .map(([n]) => n).filter(n => values[n] === undefined || values[n] === '');
        if (missing.length) {
          setResult({
            ok: false,
            error: `Brakujące pola: ${missing.join(', ')}`
          });
          return;
        }
        const payload = {
          case: caseVal
        };
        for (const [k, v] of Object.entries(values)) payload[k] = parseFloat(String(v));
        const res = await fetch('./api/collide.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        setResult(data);
        draw(data);
      }

      function draw(data = result) {
        const viz = vizRef.current;
        if (!viz) return;
        viz.clear();
        viz.drawAxes();
        if (!data || !data.ok) {
          return;
        }
        viz.banner(data.hit ? 'Collision: YES' : 'Collision: NO', data.hit);
        const A = data.a,
          B = data.b;
        switch (data.case) {
          case 'point-point':
            viz.drawPoint(A.x, A.y, '#7aa2f7', 'A');
            viz.drawPoint(B.x, B.y, '#a6da95', 'B');
            break;
          case 'point-circle':
            viz.drawPoint(A.x, A.y, '#7aa2f7', 'P');
            viz.drawCircle(B.cx, B.cy, B.r, '#a6da95', 'C');
            break;
          case 'circle-circle':
            viz.drawCircle(A.cx, A.cy, A.r, '#7aa2f7', 'C1');
            viz.drawCircle(B.cx, B.cy, B.r, '#a6da95', 'C2');
            break;
          case 'point-rect':
            viz.drawPoint(A.x, A.y, '#7aa2f7', 'P');
            viz.drawRect(B.x, B.y, B.w, B.h, '#a6da95', 'R');
            break;
          case 'rect-rect':
            viz.drawRect(A.x, A.y, A.w, A.h, '#7aa2f7', 'R1');
            viz.drawRect(B.x, B.y, B.w, B.h, '#a6da95', 'R2');
            break;
          case 'circle-rect':
            viz.drawCircle(A.cx, A.cy, A.r, '#7aa2f7', 'C');
            viz.drawRect(B.x, B.y, B.w, B.h, '#a6da95', 'R');
            break;
          case 'triangle-point':
            viz.drawTriangle(A.x1, A.y1, A.x2, A.y2, A.x3, A.y3, '#7aa2f7', 'T');
            viz.drawPoint(B.x, B.y, '#a6da95', 'P');
            break;
          case 'triangle-circle':
            viz.drawTriangle(A.x1, A.y1, A.x2, A.y2, A.x3, A.y3, '#7aa2f7', 'T');
            viz.drawCircle(B.cx, B.cy, B.r, '#a6da95', 'C');
            break;
        }
      }

      return React.createElement(React.Fragment, null, [
        React.createElement('div', {
          className: 'card'
        }, [
          React.createElement('label', {
            htmlFor: 'case'
          }, 'Wybierz parę figur'),
          React.createElement('select', {
            id: 'case',
            value: caseVal,
            onChange: e => {
              setCaseVal(e.target.value);
              setValues({});
              setResult(null);
            }
          }, Object.keys(SCHEMA).map(k => React.createElement('option', {
            key: k,
            value: k
          }, k))),
          React.createElement('div', {
            className: 'subtle',
            style: {
              marginTop: '6px'
            }
          }, 'W każdym wariancie widzisz inne pola wejściowe.')
        ]),
        React.createElement('div', {
          className: 'cols'
        }, [
          React.createElement(Box, {
            title: schema.a.title
          }, React.createElement(SectionFields, {
            prefix: 'a',
            schema: schema.a,
            values,
            setField
          })),
          React.createElement(Box, {
            title: schema.b.title
          }, React.createElement(SectionFields, {
            prefix: 'b',
            schema: schema.b,
            values,
            setField
          }))
        ]),
        React.createElement('div', {
          className: 'card'
        }, [
          React.createElement('div', {
            className: 'grid-3'
          }, [
            React.createElement('button', {
              className: 'primary',
              onClick: submit
            }, 'Sprawdź kolizję i narysuj'),
            React.createElement('button', {
              onClick: applyPreset
            }, 'Wstaw przykładowe wartości'),
            React.createElement('button', {
              onClick: () => {
                setValues({});
                setResult(null);
              }
            }, 'Wyczyść formularz')
          ]),
          React.createElement('div', {
            style: {
              marginTop: '10px'
            }
          }, result ? (result.ok ? (result.hit ? React.createElement('span', {
            className: 'good'
          }, 'Kolizja: TAK') : React.createElement('span', {
            className: 'bad'
          }, 'Kolizja: NIE')) : React.createElement('span', {
            className: 'bad'
          }, `Błąd: ${result.error}`)) : React.createElement('span', null, 'Wpisz dane i uruchom test'))
        ])
      ]);
    }

    ReactDOM.render(React.createElement(App), document.getElementById('root'));
  </script>
</body>

</html>