import json

log_path = r"C:\Users\m s i\.gemini\antigravity\brain\0a95d0e0-7b4b-4285-9bb0-211af0b89e02\.system_generated\logs\transcript.jsonl"

with open(log_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            step = json.loads(line)
            step_idx = step.get("step_index", 0)
            step_str = json.dumps(step)
            if "AppSidebar.vue" in step_str or "AdminLayout.vue" in step_str:
                # If there's a write or edit tool
                tool_calls = step.get("tool_calls", [])
                for tc in tool_calls:
                    name = tc.get("name")
                    args = tc.get("args", {})
                    target = args.get("TargetFile", "")
                    if name in ["write_to_file", "replace_file_content", "multi_replace_file_content"]:
                        print(f"Step {step_idx}: Tool {name} on {target}")
        except Exception as e:
            pass
print("Done.")
