// Final Full JavaScript Code for Auto Selecting DST, EMB, and Previewing Gallery Image


document.addEventListener('DOMContentLoaded', function () {
// Final Full JavaScript Code for Auto Selecting DST, EMB, and Previewing Gallery Image

function autoSelectDesignFiles() {
    const folderInput = document.getElementById('parent_folder');
    const designName = document.getElementById('design_name').value.trim().toLowerCase();

    if (!folderInput.files.length) {
        alert('❗ Please select the "MyDesigns" folder.');
        return;
    }

    if (!designName) {
        alert('❗ Please enter the design name (e.g., DesignA).');
        return;
    }

    const files = Array.from(folderInput.files);

    const dstFile = files.find(f =>
        f.name.toLowerCase() === `${designName}.dst` &&
        /[\/\\]dst[\/\\]/i.test(f.webkitRelativePath)
    );

    const embE4File = files.find(f =>
        f.name.toLowerCase() === `${designName}.emb` &&
        /[\/\\]emb_e4[\/\\]/i.test(f.webkitRelativePath)
    );

    const embW6File = files.find(f =>
        f.name.toLowerCase() === `${designName}.emb` &&
        /[\/\\]emb_w6[\/\\]/i.test(f.webkitRelativePath)
    );

    const imageFile = files.find(f =>
        f.name.toLowerCase().startsWith(designName) &&
        /\.(jpg|jpeg|png|webp)$/i.test(f.name) &&
        /[\/\\]img[\/\\]/i.test(f.webkitRelativePath)
    );

    function assignFile(inputId, statusId, file) {
        const input = document.getElementById(inputId);
        const status = document.getElementById(statusId);
        if (!input || !status || !file) {
            if (status) status.textContent = '❌ Not found';
            return false;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        status.textContent = `✅ ${file.name}`;
        return true;
    }

    const dstOK = assignFile('dst-file', 'dst-status', dstFile);
    const e4OK = assignFile('emb-e4-file', 'emb-e4-status', embE4File);
    const w6OK = assignFile('emb-w6-file', 'emb-w6-status', embW6File);

    const galleryInput = document.getElementById('design-gallery');
    const previewImg = document.getElementById('preview-image');
    const galleryNote = document.getElementById('gallery-note');
    const galleryStatus = document.getElementById('gallery-status');
    const placeholder = document.getElementById('preview-placeholder');

    if (imageFile && galleryInput) {
        const dt = new DataTransfer();
        dt.items.add(imageFile);
        galleryInput.files = dt.files;

        if (previewImg && imageFile) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewImg.alt = "Design Preview";
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.onerror = function (e) {
                console.error("[Preview] FileReader error:", e);
            };
            reader.readAsDataURL(imageFile);
        }

        if (galleryStatus) {
            galleryStatus.textContent = `✅ ${imageFile.name}`;
        }

        if (galleryNote) {
            galleryNote.style.display = 'block';
            galleryNote.innerText = '📷 Preview loaded from /IMG/. Click the preview or reselect to confirm.';
        }
    } else {
        if (galleryNote) {
            galleryNote.style.display = 'block';
            galleryNote.innerText = '❌ No matching image found in /IMG/ folder.';
        }
        if (previewImg) {
            previewImg.style.display = 'none';
        }
        if (galleryStatus) {
            galleryStatus.textContent = '❌ No file chosen';
        }
    }

    if (dstOK && e4OK && w6OK) {
        alert(`✅ All design files for "${designName}" selected successfully.`);
    } else {
        let msg = `⚠️ Some files are missing for "${designName}":\n`;
        if (!dstOK) msg += "- DST not found\n";
        if (!e4OK) msg += "- EMB E4 not found\n";
        if (!w6OK) msg += "- EMB W6 not found\n";
        alert(msg);
    }
}

function updateFileName(input, statusId) {
    const file = input.files[0];
    const status = document.getElementById(statusId);
    const previewImg = document.getElementById("preview-image");
    const placeholder = document.getElementById("preview-placeholder");

    status.textContent = file ? `✅ ${file.name}` : '❌ No file chosen';

    if (input.name === 'gallery' && file && previewImg) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

});


