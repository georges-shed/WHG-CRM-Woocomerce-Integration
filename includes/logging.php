<?php
// Path to the log file
$log_file = plugin_dir_path(__FILE__) . 'ghl_api_log.txt';

// Check if the log file exists
if (file_exists($log_file)) {
    // Read the contents of the log file
    $log_contents = file_get_contents($log_file);

    // Split the log contents into individual entries
    $log_entries = preg_split('/\n(?=\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $log_contents, -1, PREG_SPLIT_NO_EMPTY);

    // Function to convert log entry to associative array
    function parse_log_entry($entry) {
        $lines = explode("\n", $entry);
        $result = [];
        $current_key = null;

        foreach ($lines as $line) {
            if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) - (.+?): (.+)$/', $line, $matches)) {
                $result['timestamp'] = $matches[1];
                $result['type'] = $matches[2];
                $current_key = $matches[2];
                $result[$current_key] = $matches[3];
            } elseif (preg_match('/^\s*\[(.+?)\] => (.+)$/', $line, $matches)) {
                if (!isset($result[$current_key]) || !is_array($result[$current_key])) {
                    $result[$current_key] = [];
                }
                $result[$current_key][$matches[1]] = $matches[2];
            }
        }

        return $result;
    }

    // Function to extract Order ID or Quote ID from the name field
    function extract_id($name) {
        if (preg_match('/Order ID: (\d+)/', $name, $matches)) {
            return 'Order ID: ' . $matches[1];
        } elseif (preg_match('/Quote ID: (\d+)/', $name, $matches)) {
            return 'Quote ID: ' . $matches[1];
        }
        return 'ID not found';
    }

    // Recursive function to handle array data and print it as a table
    function print_table($data) {
        echo '<table border="1">';
        echo '<tr><th>Key</th><th>Value</th></tr>';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                echo '<tr><td>' . esc_html($key) . '</td><td>';
                foreach ($value as $sub_key => $sub_value) {
                    if (is_array($sub_value)) {
                        echo '<strong>' . esc_html($sub_key) . '</strong>: <pre>' . esc_html(print_r($sub_value, true)) . '</pre><br>';
                    } else {
                        echo esc_html($sub_key) . ': ' . esc_html($sub_value) . '<br>';
                    }
                }
                echo '</td></tr>';
            } else {
                echo '<tr><td>' . esc_html($key) . '</td><td>' . esc_html($value) . '</td></tr>';
            }
        }
        echo '</table><br>';
    }

    // Display each log entry in a table
    foreach ($log_entries as $entry) {
        $parsed_entry = parse_log_entry($entry);
        $id = isset($parsed_entry['GHL Opportunity Created']['name']) ? extract_id($parsed_entry['GHL Opportunity Created']['name']) : 'ID not found';
        echo '<h3>' . esc_html($parsed_entry['timestamp'] . ' - ' . $id) . '</h3>';

        if (isset($parsed_entry['GHL Opportunity Created'])) {
            // Extract and display the opportunity name
            $opportunity_data = $parsed_entry['GHL Opportunity Created'];
            if (isset($opportunity_data['name'])) {
                $opportunity_name = is_array($opportunity_data['name']) ? implode(', ', $opportunity_data['name']) : $opportunity_data['name'];
            } else {
                $opportunity_name = 'No name available';
            }
            echo '<h4>Opportunity Name: ' . esc_html($opportunity_name) . '</h4>';

            // Display the rest of the opportunity data
            print_table($opportunity_data);
        }
    }
} else {
    echo '<p>No log entries found.</p>';
}
?>
