import os
from PIL import Image

def optimize_image(input_path, output_path, max_width=1200):
    try:
        with Image.open(input_path) as img:
            if img.mode == 'P':
                img = img.convert('RGBA')
            
            # Resize if too large
            if img.width > max_width:
                ratio = max_width / img.width
                new_size = (max_width, int(img.height * ratio))
                img = img.resize(new_size, Image.Resampling.LANCZOS)
                
            img.save(output_path, 'WEBP', quality=80, method=6)
            print(f"Optimized {input_path} to {output_path}")
            # Remove original
            if os.path.exists(output_path):
                img.close()
                os.remove(input_path)
    except Exception as e:
        print(f"Failed to optimize {input_path}: {e}")

images = {
    'public/flight card.jpg': 'public/flight-card.webp',
    'public/hotel card.jpg': 'public/hotel-card.webp',
    'public/discount card.png': 'public/discount-card.webp',
    'public/HERO PIC.png': 'public/hero-pic.webp'
}

for inp, out in images.items():
    if os.path.exists(inp):
        optimize_image(inp, out)
    else:
        print(f"Skipped {inp}, not found")
