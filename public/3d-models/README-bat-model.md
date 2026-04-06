# Cricket Bat GLB Contract

This repo uses a simplified bat model that is optimized for runtime front/back texture swaps.

## Required mesh names

- `Bat_Front`
- `Bat_Back`
- `Bat_Edge`

The viewer treats `Bat_Front` and `Bat_Back` as the only customizable surfaces. Any replacement GLB must preserve those names exactly.

## UV contract

- `Bat_Front` must map the full visible front face to UV `0..1`.
- `Bat_Back` must map the full visible back face to UV `0..1`.
- `Bat_Back` should be UV-flipped horizontally so readable back graphics stay readable.
- Target artboards should use the same ratio as the bundled test posters: `800x1400`.

## How the repo generates the model

Run:

```powershell
python scripts/gen_bat_glb.py public/3d-models/cricket_bat.glb
```

That script produces:

- `Bat_Front`: flat front face with clean UVs
- `Bat_Back`: flat back face with clean UVs
- `Bat_Edge`: perimeter wood mesh

## Why this contract exists

- Texture updates happen without reloading the model.
- Front and back images can change independently.
- The edge stays wood-toned, which keeps the bat looking like a real surface instead of a sticker plane.

## Manual Blender checklist for future reference models

1. Separate the front striking face into `Bat_Front`.
2. Separate the rear face into `Bat_Back`.
3. Keep the perimeter in `Bat_Edge`.
4. Remove floating sticker planes and baked overlays.
5. Recalculate normals.
6. UV unwrap front and back independently to fill the UV square cleanly.
7. Export as GLB and verify the final mesh names.
