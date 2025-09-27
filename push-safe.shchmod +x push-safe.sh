#!/bin/bash
# Script Push Aman ke GitHub

# Step 1: Cek branch aktif

./push-safe.sh "Pesan commit kamu"

BRANCH=$(git branch --show-current)
echo "📌 Branch aktif: $BRANCH"

# Step 2: Commit semua perubahan
echo "📦 Menyimpan perubahan..."
git add .
git commit -m "${1:-Auto commit}" || echo "⚠️ Tidak ada perubahan untuk di-commit"

# Step 3: Pull dari GitHub pakai rebase
echo "🔄 Sinkronisasi dengan GitHub..."
git pull origin "$BRANCH" --rebase

# Step 4: Push ke GitHub
echo "🚀 Mengirim ke GitHub..."
git push origin "$BRANCH"

echo "✅ Selesai!"

