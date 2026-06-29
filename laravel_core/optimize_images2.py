import os
from PIL import Image

def optimize_image(input_path, output_path, max_width=1200):
    try:
        with Image.open(input_path) as img:
            if img.mode == 'P':
                img = img.convert('RGBA')
            if img.width > max_width:
                ratio = max_width / img.width
                new_size = (max_width, int(img.height * ratio))
                img = img.resize(new_size, Image.Resampling.LANCZOS)
            img.save(output_path, 'WEBP', quality=80, method=6)
            print(f"Optimized {input_path} to {output_path}")
            if os.path.exists(output_path):
                img.close()
                os.remove(input_path)
    except Exception as e:
        print(f"Failed to optimize {input_path}: {e}")

images = {
    'public/hero-pic-optimized.jpg': 'public/hero-pic-optimized.webp',
    'public/hero-pic-mobile.jpg': 'public/hero-pic-mobile.webp'
}

for inp, out in images.items():
    if os.path.exists(inp):
        optimize_image(inp, out)
    else:
        print(f"Skipped {inp}, not found")
