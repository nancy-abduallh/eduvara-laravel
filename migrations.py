# merge_to_float16.py  — run locally in your venv
# Output: models/script-merged-f16/  (~7 GB, 2 shards)

import torch
from transformers import AutoTokenizer, AutoModelForCausalLM
from peft import PeftModel, PeftConfig
import time
import sys

ADAPTER = r"C:\xampp\htdocs\eduvara\ai-service\models\script-adapter"
BASE    = r"C:\xampp\htdocs\eduvara\ai-service\models\Qwen2-7B-Instruct"
OUT     = r"C:\xampp\htdocs\eduvara\ai-service\models\script-merged-f16"

print("=" * 50)
print("Starting adapter merge process...")
print(f"Adapter: {ADAPTER}")
print(f"Base model: {BASE}")
print(f"Output: {OUT}")
print("=" * 50)

print("\n1. Loading tokenizer...")
start = time.time()
tok = AutoTokenizer.from_pretrained(ADAPTER, trust_remote_code=True)
if tok.pad_token is None:
    tok.pad_token = tok.eos_token
print(f"   ✓ Tokenizer loaded in {time.time() - start:.1f}s")

print("\n2. Loading base model (float16) - this will take 5-10 minutes...")
print("   (CPU loading of 7B model requires ~14GB RAM)")
start = time.time()
sys.stdout.flush()

base = AutoModelForCausalLM.from_pretrained(
    BASE,
    torch_dtype=torch.float16,
    device_map="cpu",
    trust_remote_code=True,
    low_cpu_mem_usage=True,
)
base.resize_token_embeddings(len(tok))
print(f"   ✓ Base model loaded in {time.time() - start:.1f}s")

print("\n3. Applying LoRA adapter and merging...")
start = time.time()
merged = PeftModel.from_pretrained(base, ADAPTER)
print(f"   ✓ Adapter applied in {time.time() - start:.1f}s")

print("   Merging weights...")
start = time.time()
merged = merged.merge_and_unload()
merged.eval()
print(f"   ✓ Merged in {time.time() - start:.1f}s")

print(f"\n4. Saving merged model to {OUT} ...")
start = time.time()
merged.save_pretrained(OUT, safe_serialization=True, max_shard_size="4GB")
tok.save_pretrained(OUT)
print(f"   ✓ Saved in {time.time() - start:.1f}s")

print("\n" + "=" * 50)
print("✅ Done! Merged model saved to:", OUT)
print("=" * 50)