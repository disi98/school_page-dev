<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../statics/css/output/tw_output.css">
  <link rel="icon" href="../statics/img/PCEAschool.png" type="image/icon type">
  <title>Gallery(Service) - PCEA Schools</title>
  <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body>

  <div class="fixed bottom-4 ml-3 bg-blue-800 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition-all">
    <a href="gallary.html" class="hover:underline">Back to Galarry</a>
    <a href="service.php" class="hover:underline font-bold px-4">Service</a>
    <a href="stuff.php" class="hover:underline font-bold">Staff</a>
  </div>

  <section class="mx-auto bg-gray-400 grid justify-items-center">
    <div class="flex container my-12 bg-gray-200 flex-wrap md:grid md:grid-cols-3 lg:grid-cols-4 justify-items-center  items-center p-4">
      <!-- Lightbox Container -->
      <div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4 cursor-pointer">
        <img id="lightbox-image" class="max-w-full max-h-full rounded-lg" src="">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-4xl">&times;</button>
      </div>

      <?php
        $imageDir = '../statics/img/service/';
        $thumbDir = '../statics/img/service/thumbnails/';
        $thumbWidth = 300; // Desired thumbnail width
        $thumbHeight = 200; // Desired thumbnail height

        // Create thumbnails directory if it doesn't exist
        if (!is_dir($thumbDir)) {
          mkdir($thumbDir, 0777, true);
        }

        if (is_dir($imageDir)) {
          $files = scandir($imageDir);
          $images = array_filter($files, function($file) {
              return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
          });

          foreach ($images as $filename) {
            $safeFilename = htmlspecialchars($filename);
            $imagePath = $imageDir . $safeFilename;
            $thumbPath = $thumbDir . $safeFilename;

            // Generate thumbnail if it does not exist
            if (!file_exists($thumbPath)) {
              createThumbnail($imagePath, $thumbPath, $thumbWidth, $thumbHeight);
            }

            echo '
            <div class="p-2 flex flex-col items-center">
              <img class="w-72 h-48 object-cover rounded-lg cursor-zoom-in"
                src="' . $thumbPath . '"
                data-src="' . $imagePath . '"
                alt=""
                onclick="openLightbox(this.dataset.src)"
                loading="lazy"
                fetchpriority="low">
            </div>';
          }
        } else {
          echo '<p>Image directory not found</p>';
        }

        /**
         * Create a thumbnail image of fixed dimensions.
         *
         * @param string $src Path to the source image.
         * @param string $dest Path to save the thumbnail.
         * @param int $width Thumbnail width.
         * @param int $height Thumbnail height.
         */
        function createThumbnail($src, $dest, $width, $height) {
          $imageInfo = getimagesize($src);
          if (!$imageInfo) return;

          list($origWidth, $origHeight) = $imageInfo;
          $type = $imageInfo[2];

          // Load image based on type
          switch ($type) {
            case IMAGETYPE_JPEG:
              $image = imagecreatefromjpeg($src);
              break;
            case IMAGETYPE_PNG:
              $image = imagecreatefrompng($src);
              break;
            case IMAGETYPE_GIF:
              $image = imagecreatefromgif($src);
              break;
            case IMAGETYPE_WEBP:
              $image = imagecreatefromwebp($src);
              break;
            default:
              return; // Unsupported format
          }

          // Create blank thumbnail
          $thumb = imagecreatetruecolor($width, $height);

          // Preserve transparency for PNG & GIF
          if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
          }

          // Resize and crop: fill thumbnail dimensions while maintaining aspect ratio
          // Calculate aspect ratios
          $srcRatio = $origWidth / $origHeight;
          $thumbRatio = $width / $height;
          if ($srcRatio > $thumbRatio) {
              // Source is wider than thumbnail
              $newHeight = $height;
              $newWidth = (int)($height * $srcRatio);
          } else {
              // Source is taller or equal ratio to thumbnail
              $newWidth = $width;
              $newHeight = (int)($width / $srcRatio);
          }
          // Resize the image first to an intermediate size
          $tempImage = imagecreatetruecolor($newWidth, $newHeight);
          if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($tempImage, imagecolorallocatealpha($tempImage, 0, 0, 0, 127));
            imagealphablending($tempImage, false);
            imagesavealpha($tempImage, true);
          }
          imagecopyresampled($tempImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

          // Now crop the center portion for the thumbnail
          $x = ($newWidth - $width) / 2;
          $y = ($newHeight - $height) / 2;
          imagecopy($thumb, $tempImage, 0, 0, $x, $y, $width, $height);

          // Save thumbnail with appropriate quality
          switch ($type) {
            case IMAGETYPE_JPEG:
              imagejpeg($thumb, $dest, 85);
              break;
            case IMAGETYPE_PNG:
              imagepng($thumb, $dest, 8);
              break;
            case IMAGETYPE_GIF:
              imagegif($thumb, $dest);
              break;
            case IMAGETYPE_WEBP:
              imagewebp($thumb, $dest, 85);
              break;
          }

          imagedestroy($image);
          imagedestroy($tempImage);
          imagedestroy($thumb);
        }
      ?>
    </div>
  </section>

  <script>
    // Lightbox functionality remains unchanged
    function openLightbox(src) {
      const lightbox = document.getElementById('lightbox');
      const lightboxImage = document.getElementById('lightbox-image');
      lightboxImage.src = src;
      lightbox.classList.remove('hidden');
      document.body.style.overflow = 'hidden'; // Prevent scrolling
    }

    function closeLightbox() {
      const lightbox = document.getElementById('lightbox');
      lightbox.classList.add('hidden');
      document.body.style.overflow = 'auto'; // Restore scrolling
    }

    // Close lightbox when clicking outside the image
    document.getElementById('lightbox').addEventListener('click', (e) => {
      if (e.target.id === 'lightbox') {
        closeLightbox();
      }
    });

    // Close lightbox with ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !document.getElementById('lightbox').classList.contains('hidden')) {
        closeLightbox();
      }
    });

    // Lazy-load full images using IntersectionObserver
    document.addEventListener("DOMContentLoaded", function () {
      const images = document.querySelectorAll("img[data-src]");
      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            observer.unobserve(img);
          }
        });
      });
      images.forEach(img => observer.observe(img));
    });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
