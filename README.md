# Detect Collisions — PHP 8 OOP + React + Canvas

Nowa, obiektowa wersja projektu do wykrywania kolizji między obiektami geometrycznymi.

## Stos technologiczny

- **PHP 8+ (OOP)** — klasy `Point`, `Circle`, `Rectangle`, `Triangle` i statyczny serwis `CollisionService`.
- **API (JSON)** — endpoint `public/api/collide.php`, który przyjmuje dane i zwraca wynik kolizji.
- **Frontend: React (CDN) + Canvas** — elegancki formularz do wpisywania współrzędnych i natychmiastowa wizualizacja na canvasie.
- **PHPUnit** — testy jednostkowe dla wszystkich przypadków.

## Struktura katalogów

```
collisions_oop_react/
├─ public/
│  ├─ index.php          # UI (React + Canvas)
│  └─ api/
│     └─ collide.php     # JSON API
├─ src/
│  ├─ autoload.php
│  ├─ Geometry/
│  │  ├─ Shape.php
│  │  ├─ Point.php
│  │  ├─ Circle.php
│  │  ├─ Rectangle.php
│  │  └─ Triangle.php
│  └─ Collision/
│     └─ CollisionService.php
├─ tests/
│  ├─ bootstrap.php
│  └─ CollisionServiceTest.php
├─ phpunit.xml
└─ README.md
```

## Uruchomienie

1. Skopiuj katalog `public/` na serwer z PHP (np. lokalnie `php -S localhost:8000 -t public`).
2. Otwórz `http://localhost:8000` w przeglądarce.
3. Wybierz przypadek, wpisz dane, kliknij **Sprawdź kolizję i narysuj**.

## API

`POST /api/collide.php` (JSON)

Przykład (circle-circle):

```json
{
  "case": "circle-circle",
  "c1x": -150, "c1y": 0, "r1": 140,
  "c2x": 50,   "c2y": 0, "r2": 120
}
```

Wynik:

```json
{
  "ok": true,
  "case": "circle-circle",
  "hit": true,
  "a": { "cx": -150, "cy": 0, "r": 140, "name": "C1" },
  "b": { "cx": 50, "cy": 0, "r": 120, "name": "C2" }
}
```

## Testy

Wymagany PHPUnit (np. `composer global require phpunit/phpunit`). Z katalogu projektu:

```bash
phpunit
# lub
vendor/bin/phpunit
```

Konfiguracja w `phpunit.xml`, bootstrap ładuje `src/autoload.php`.

## Uwaga dotycząca układu współrzędnych

- Oś **X** rośnie w prawo, oś **Y** rośnie w górę (koordynaty „matematyczne”).
- Zero układu jest w środku płótna. Warstwa rysująca tłumaczy te współrzędne na układ canvasa.

## Licencja
MIT
