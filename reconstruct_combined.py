import json
import re

log_path = r"C:\Users\m s i\.gemini\antigravity\brain\0a95d0e0-7b4b-4285-9bb0-211af0b89e02\.system_generated\logs\transcript.jsonl"

def reconstruct_file_before_step(filename, limit_step):
    reconstructed_lines = {}
    with open(log_path, 'r', encoding='utf-8') as f:
        for line in f:
            try:
                step = json.loads(line)
                step_idx = step.get("step_index", 0)
                if step_idx >= limit_step:
                    continue
                
                # Check human/agent text or tool calls/results
                text_to_search = json.dumps(step)
                if filename in text_to_search:
                    # Find all "line: content" patterns in this step
                    # Typically, we have lines like "1: <script setup>"
                    # Let's match line number patterns
                    matches = re.findall(r"(?:^|\n|\\n)\s*(\d+):\s*([^\n\\]*)", text_to_search)
                    for num_str, content in matches:
                        num = int(num_str)
                        # Avoid matching random line numbers if they don't look like code
                        # We only overwrite if it's the earliest step, or if we don't have it yet
                        if num not in reconstructed_lines:
                            reconstructed_lines[num] = (step_idx, content)
            except Exception as e:
                pass
                
    if reconstructed_lines:
        sorted_keys = sorted(reconstructed_lines.keys())
        print(f"File {filename}: found {len(reconstructed_lines)} lines out of max line {sorted_keys[-1] if sorted_keys else 0}")
        # Build the content
        content_lines = []
        for i in range(1, sorted_keys[-1] + 1):
            if i in reconstructed_lines:
                # remove JSON escaping if any (e.g. \" to ")
                val = reconstructed_lines[i][1]
                # clean up escaping
                val = val.replace('\\"', '"').replace('\\\\', '\\').replace('\\/', '/')
                content_lines.append(val)
            else:
                content_lines.append(f"// MISSING LINE {i}")
        
        out_name = f"{filename}_reconstructed_combined.vue"
        with open(out_name, 'w', encoding='utf-8') as out_f:
            out_f.write("\n".join(content_lines))
        print(f"Wrote to {out_name}")

reconstruct_file_before_step("AppSidebar.vue", 4600)
reconstruct_file_before_step("AdminLayout.vue", 4600)
