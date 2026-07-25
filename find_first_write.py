import json

log_path = r"C:\Users\m s i\.gemini\antigravity\brain\0a95d0e0-7b4b-4285-9bb0-211af0b89e02\.system_generated\logs\transcript.jsonl"

with open(log_path, 'r', encoding='utf-8') as f:
    count = 0
    for line in f:
        try:
            step = json.loads(line)
            step_str = json.dumps(step)
            if "AppSidebar.vue" in step_str or "AdminLayout.vue" in step_str:
                print(f"Step {step.get('step_index')}: type={step.get('type')}, status={step.get('status')}")
                # Print keys of the step
                print("Keys:", list(step.keys()))
                if "tool_calls" in step:
                    print("Tool Calls:", json.dumps(step["tool_calls"], indent=2)[:500])
                count += 1
                if count >= 5:
                    break
        except Exception as e:
            pass
