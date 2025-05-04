import os
import json

def rename_images_and_write_index(folder_path, extensions=None, prefix="service_", json_filename="images.json"):
    """
    Rename image files in folder_path to prefix1.ext, prefix2.ext, ..., prefixN.ext,
    and create a JSON file listing each new filename (with extensions).

    :param folder_path:   Path to the directory with images.
    :param extensions:    Iterable of file extensions to include (e.g. ['.jpg', '.png']).
                          If None, defaults to common image types.
    :param prefix:        The prefix to use for renaming (default 'img').
    :param json_filename: The name of the JSON file to write (default 'images.json').
    """
    # Default set of common image extensions
    if extensions is None:
        extensions = {'.jpg', '.jpeg', '.png', '.gif', '.bmp', '.tiff'}

    # Gather and sort image files
    files = sorted(
        f for f in os.listdir(folder_path)
        if os.path.splitext(f)[1].lower() in extensions
    )

    new_filenames = []
    # Rename each file in order
    for count, old_name in enumerate(files, start=1):
        ext = os.path.splitext(old_name)[1]              # e.g. '.jpg'
        new_name = f"{prefix}{count}{ext}"               # e.g. 'img1.jpg'

        src = os.path.join(folder_path, old_name)
        dst = os.path.join(folder_path, new_name)
        os.rename(src, dst)

        new_filenames.append(new_name)
        print(f"Renamed: {old_name} → {new_name}")

    # Write the JSON index of new filenames (with extensions)
    json_path = os.path.join(folder_path, json_filename)
    with open(json_path, 'w', encoding='utf-8') as jf:
        json.dump(new_filenames, jf, indent=2)

    print(f"\nWrote {len(new_filenames)} entries to {json_filename}")

if __name__ == "__main__":
    # Example usage — update this path to your image folder:
    rename_images_and_write_index("./trips")
