import json

log_path = r"C:\Users\m s i\.gemini\antigravity\brain\0a95d0e0-7b4b-4285-9bb0-211af0b89e02\.system_generated\logs\transcript.jsonl"

with open(log_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            step = json.loads(line)
            step_idx = step.get("step_index", 0)
            step_str = json.dumps(step)
            if "SidebarProvider" in step_str and ("write_to_file" in step_str or "replace_file_content" in step_str or "multi_replace" in step_str):
                print(f"Step {step_idx}: edit containing SidebarProvider")
                # print the replacement or target
                tool_calls = step.get("tool_calls", [])
                for tc in tool_calls:
                    print(f"  Tool: {tc.get('name')}")
                    args = tc.get("args", {})
                    print(f"  Target: {args.get('TargetFile')}")
                    print(f"  Instruction: {args.get('Instruction')}")
        except Exception as e:
            pass
print("Done searching.")
