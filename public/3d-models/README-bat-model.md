# Cricket Bat 3D Model — Blender Creation Guide

## Requirements

- **Blender**: 3.6 LTS or 4.x
- **Export format**: glTF Binary (`.glb`)
- **Save to**: `public/3d-models/cricket-bat.glb`

---

## 1. Mesh Structure (Exact Names Required)

The Three.js configurator locates meshes by name. Use **exactly** these object names in the Outliner:

| Object Name | Description |
|---|---|
| `Bat_Body` | Main willow blade + handle shaft |
| `Bat_Sticker` | Front face sticker panel (decal area on the blade face) |
| `Bat_Grip` | Handle grip wrapping (top ~25 cm of the handle) |
| `Bat_Toe_Round` | Toe variant — rounded bottom edge |
| `Bat_Toe_Flat` | Toe variant — flat/squared bottom edge |
| `Bat_Profile_Full` | Full profile blade (thicker, traditional shape) |
| `Bat_Profile_Duckbill` | Duckbill profile blade (concave scoop at toe) |

> **Important**: Profile and toe variants are toggled via visibility in Three.js. Model both variants as separate meshes positioned identically — only one of each pair is visible at a time.

---

## 2. Dimensions

Real-world cricket bat proportions (Short Handle size):

| Measurement | Value |
|---|---|
| Total length | ~0.85 m (85 cm) |
| Blade width (sweet spot) | ~0.108 m (10.8 cm) |
| Blade thickness | ~0.040 m (4 cm) at thickest |
| Handle length | ~0.30 m (30 cm) from shoulder to top |
| Grip coverage | ~0.25 m (25 cm) of handle |
| Toe height | ~0.015 m (1.5 cm) |

Model at **real-world scale** (1 Blender unit = 1 meter). The configurator auto-scales to fit the viewport.

---

## 3. Materials (One Per Mesh)

Create and assign these materials:

| Material Name | Assigned To | Base Color | Notes |
|---|---|---|---|
| `Mat_Body` | `Bat_Body` | `#D4A574` (natural willow) | Roughness ~0.7, subtle wood grain texture optional |
| `Mat_Sticker` | `Bat_Sticker` | `#7AFF66` (default green) | Roughness ~0.4, slightly glossy |
| `Mat_Grip` | `Bat_Grip` | `#FFFFFF` (default white) | Roughness ~0.6, rubber-like |
| `Mat_Toe` | `Bat_Toe_Round`, `Bat_Toe_Flat` | `#D4A574` | Same as body |
| `Mat_Profile` | `Bat_Profile_Full`, `Bat_Profile_Duckbill` | `#D4A574` | Same as body |

> Three.js will override `Mat_Sticker` and `Mat_Grip` colors dynamically. The initial colors above are just defaults.

---

## 4. UV Unwrapping

### Bat_Body UV (Critical for Engraving)

The **front face** of the blade (the flat hitting surface) must occupy **~60% of UV space**. This is the region where the engraving `CanvasTexture` will be applied.

Steps:
1. Select `Bat_Body`
2. Enter Edit Mode → Select front face polygons
3. UV Unwrap (Project from View — front orthographic works well)
4. In the UV Editor, scale the front face island to fill ~60% of the UV square
5. Pack remaining islands (back, edges, handle) into the remaining ~40%

### Other Meshes

Standard Smart UV Project is fine for `Bat_Sticker`, `Bat_Grip`, toe, and profile meshes.

---

## 5. Modeling Tips

### Blade
- Start with a cube, scale to blade dimensions
- Add loop cuts for the sweet spot bulge
- Use Subdivision Surface (level 1-2) for smooth edges
- The spine (back ridge) should be ~2 cm wide

### Handle
- Cylinder with 12-16 segments
- Taper slightly from shoulder to top
- The splice joint (where blade meets handle) should be a smooth transition

### Sticker Panel
- Duplicate the front face of the blade
- Offset by ~0.5 mm forward (to prevent z-fighting)
- Cover the central ~70% of the blade face

### Grip
- Cylinder wrapping the handle
- Offset ~0.5 mm outward from handle surface
- Spiral texture optional (can be baked into normal map)

### Toe Variants
- `Bat_Toe_Round`: Rounded bottom edge, ~1.5 cm radius
- `Bat_Toe_Flat`: Squared/flat bottom edge

### Profile Variants
- `Bat_Profile_Full`: Traditional full blade, even thickness
- `Bat_Profile_Duckbill`: Concave scoop near the toe, thinner at bottom

---

## 6. Export Settings

1. **File → Export → glTF 2.0 (.glb/.gltf)**
2. Settings:
   - **Format**: glTF Binary (`.glb`)
   - **Include**: Selected Objects (or all, if only bat objects exist)
   - **Transform**:
     - **+Y Up** ✓
   - **Mesh**:
     - **Apply Modifiers** ✓
     - **UVs** ✓
     - **Normals** ✓
     - **Vertex Colors** (if used) ✓
   - **Material**: Export materials
   - **Compression**:
     - **Draco Compression** ✓ (reduces file size ~60-80%)
     - Compression Level: 6
     - Quantization Position: 14
     - Quantization Normal: 10
3. Save as: `public/3d-models/cricket-bat.glb`

---

## 7. Verification Checklist

After export, verify in the [glTF Viewer](https://gltf-viewer.donmccurdy.com/):

- [ ] All 7 meshes are present with correct names
- [ ] Materials display correct default colors
- [ ] No mesh is scaled to 0 or inverted
- [ ] UV layout shows front face prominently for `Bat_Body`
- [ ] File size is under 2 MB (with Draco compression)
- [ ] Model is oriented correctly: bat standing upright, face toward camera

---

## 8. Alternative: Using a Free Model

If creating from scratch is not feasible:

1. Download a cricket bat mesh from Sketchfab or TurboSquid (CC license)
2. Import into Blender
3. Separate the mesh into the 7 required objects
4. Rename each object to match the names in §1
5. Assign materials per §3
6. UV unwrap `Bat_Body` per §4
7. Export per §6
