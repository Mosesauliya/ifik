<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $tbl_user = 'user';
    protected $tbl_role = 'user_role';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->database();
        $this->tbl_user = $this->db->table_exists('user') ? 'user' : 'users';
        $this->tbl_role = $this->db->table_exists('user_role') ? 'user_role' : 'roles';
    }

    /**
     * Standardize user object so all CI controllers/views work consistently
     */
    private function _normalize_user($user)
    {
        if (!$user) return null;
        if (!isset($user->nidn_nim)) {
            $user->nidn_nim = !empty($user->nim) ? $user->nim : (!empty($user->nip) ? $user->nip : '');
        }
        if (!isset($user->nim)) {
            $user->nim = $user->nidn_nim;
        }
        if (!isset($user->status)) {
            $user->status = (!empty($user->is_active) && $user->is_active == 1) ? 'active' : 'inactive';
        }
        if (!isset($user->password_changed)) {
            if (!empty($user->token)) {
                $user->password_changed = 0;
            } else {
                $user->password_changed = (!empty($user->is_active) && $user->is_active == 1) ? 1 : 0;
            }
        } else {
            $user->password_changed = (int)$user->password_changed;
        }
        if (empty($user->token) && !empty($user->email) && $this->db->table_exists('user_token')) {
            $tokRow = $this->db->get_where('user_token', ['email' => $user->email])->row();
            if ($tokRow && !empty($tokRow->token)) {
                $user->token = $tokRow->token;
            }
        }
        return $user;
    }

    /**
     * Get user by email address, username, or NIM/NIP
     * @param string $identity
     * @return object|null
     */
    public function get_by_email($identity)
    {
        $idStr = strtolower(trim($identity));
        $this->db->where('email', $idStr);
        if ($this->db->field_exists('username', $this->tbl_user)) {
            $this->db->or_where('username', $idStr);
        }
        if ($this->db->field_exists('nim', $this->tbl_user)) {
            $this->db->or_where('nim', $idStr);
        }
        if ($this->db->field_exists('nidn_nim', $this->tbl_user)) {
            $this->db->or_where('nidn_nim', $idStr);
        }
        $user = $this->db->get($this->tbl_user)->row();
        return $this->_normalize_user($user);
    }

    /**
     * Get user by ID
     * @param mixed $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        $id = (string)$id;
        $user = $this->db->get_where($this->tbl_user, ['id' => $id])->row();
        return $this->_normalize_user($user);
    }

    /**
     * Update user record
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $id = (string)$id;
        $fields = $this->db->list_fields($this->tbl_user);
        $cleanData = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $fields)) {
                $cleanData[$k] = $v;
            }
        }
        if (empty($cleanData)) return false;
        $this->db->where('id', $id);
        return $this->db->update($this->tbl_user, $cleanData);
    }

    /**
     * Store password reset token for a user
     * @param string $email
     * @param string $token
     * @return bool
     */
    public function set_reset_token($email, $token)
    {
        $email = strtolower(trim($email));
        $this->db->where('email', $email);
        $res = $this->db->update($this->tbl_user, [
            'token'      => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Also record in user_token table if exists
        if ($this->db->table_exists('user_token')) {
            $this->db->insert('user_token', [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ]);
        }
        return $res;
    }

    /**
     * Verify if password reset token matches for an email
     * @param string $email
     * @param string $token
     * @return object|null
     */
    public function verify_reset_token($email, $token)
    {
        $user = $this->get_by_email($email);
        if ($user && !empty($user->token) && $user->token === $token) {
            return $user;
        }
        if ($this->db->table_exists('user_token')) {
            $foundToken = $this->db->get_where('user_token', ['email' => $email, 'token' => $token])->row();
            if ($foundToken) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Reset user password using token
     * @param string $email
     * @param string $token
     * @param string $newHashedPassword
     * @return bool
     */
    public function reset_password_by_token($email, $token, $newHashedPassword)
    {
        $user = $this->verify_reset_token($email, $token);
        if (!$user) return false;

        $this->db->where('id', $user->id);
        $updatePayload = [
            'password'         => $newHashedPassword,
            'password_changed' => 1,
            'token'            => null
        ];
        if ($this->db->field_exists('updated_at', $this->tbl_user)) {
            $updatePayload['updated_at'] = date('Y-m-d H:i:s');
        }
        $res = $this->db->update($this->tbl_user, $updatePayload);

        // Clear from user_token
        if ($this->db->table_exists('user_token')) {
            $this->db->where('email', $email);
            $this->db->delete('user_token');
        }
        return $res;
    }

    /**
     * Check if user exists by NIM/NIDN or Name
     * @param string $identifier
     * @return bool
     */
    public function check_user_exists($identifier)
    {
        $identifier = trim($identifier);
        $this->db->group_start();
        if ($this->db->field_exists('nidn_nim', $this->tbl_user)) $this->db->where('nidn_nim', $identifier);
        if ($this->db->field_exists('nim', $this->tbl_user)) $this->db->or_where('nim', $identifier);
        if ($this->db->field_exists('username', $this->tbl_user)) $this->db->or_where('username', $identifier);
        if ($this->db->field_exists('name', $this->tbl_user)) $this->db->or_where('name', $identifier);
        $this->db->group_end();
        
        $query = $this->db->get($this->tbl_user);
        return $query->num_rows() > 0;
    }

    /**
     * Get all users joined with roles table and user_token table
     * @return array
     */
    public function get_all_users_with_roles()
    {
        $roleField = $this->db->field_exists('role', $this->tbl_role) ? 'role' : 'name';
        $roleDisplayField = $this->db->field_exists('display_name', $this->tbl_role) ? 'display_name' : $roleField;
        $hasTokenTable = $this->db->table_exists('user_token');
        $tokenSelect = $hasTokenTable ? ", ut.token as token_hash, ut.date_created as token_created_at" : "";

        $this->db->select("u.*, r.{$roleField} as role_slug, r.{$roleDisplayField} as role_display_name {$tokenSelect}");
        $this->db->from("{$this->tbl_user} u");
        $this->db->join("{$this->tbl_role} r", "u.role_id = r.id", 'left');
        if ($hasTokenTable) {
            $this->db->join("user_token ut", "u.email = ut.email", 'left');
        }
        $this->db->order_by('u.date_created', 'DESC');
        $this->db->order_by('u.id', 'DESC');
        $results = $this->db->get()->result_array();

        // Fetch latest email dispatch logs if log_approval_history exists
        $emailLogs = [];
        if ($this->db->table_exists('log_approval_history')) {
            $logs = $this->db->query("
                SELECT ref_id, action, created_at
                FROM log_approval_history
                WHERE modul = 'Import Email'
                ORDER BY id ASC
            ")->result_array();
            foreach ($logs as $l) {
                $emailLogs[strtolower(trim($l['ref_id']))] = [
                    'status' => ($l['action'] === 'Email Sent') ? 'terkirim' : ($l['action'] === 'Email Failed' ? 'gagal' : 'belum'),
                    'sent_at' => $l['created_at']
                ];
            }
        }

        // Standardize output
        foreach ($results as &$row) {
            if (empty($row['nidn_nim'])) {
                $row['nidn_nim'] = !empty($row['nim']) ? $row['nim'] : (!empty($row['nip']) ? $row['nip'] : '-');
            }
            if (empty($row['token']) && !empty($row['token_hash'])) {
                $row['token'] = $row['token_hash'];
            }
            $isActive = isset($row['is_active']) ? (int)$row['is_active'] : 0;
            if (!empty($row['token'])) {
                $row['password_changed'] = 0;
            } else {
                $row['password_changed'] = ($isActive === 1) ? 1 : 0;
            }
            if (empty($row['role_display_name']) && !empty($row['role_slug'])) {
                $row['role_display_name'] = $row['role_slug'];
            }

            $userEmail = strtolower(trim($row['email'] ?? ''));
            if (isset($emailLogs[$userEmail])) {
                $row['email_status'] = $emailLogs[$userEmail]['status'];
                $row['email_sent_at'] = $emailLogs[$userEmail]['sent_at'];
            } else {
                $row['email_status'] = !empty($row['email_status']) ? $row['email_status'] : 'belum';
                $row['email_sent_at'] = !empty($row['email_sent_at']) ? $row['email_sent_at'] : '-';
            }
        }
        return $results;
    }

    /**
     * Get role_id by string name / slug
     * @param string $roleName
     * @return int
     */
    public function get_role_id_by_name($roleName)
    {
        $roleName = strtolower(trim($roleName));
        $isUserRoleTable = ($this->tbl_role === 'user_role');

        if (strpos($roleName, 'mahasiswa') !== false) return $isUserRoleTable ? 4 : 5;
        if (strpos($roleName, 'dosen') !== false) return $isUserRoleTable ? 3 : 4;
        if (strpos($roleName, 'laboran') !== false) return 2;
        if (strpos($roleName, 'kaur') !== false || strpos($roleName, 'ka. ur') !== false) return $isUserRoleTable ? 2 : 3;
        if (strpos($roleName, 'koordinator') !== false || strpos($roleName, 'koordinatorta') !== false) return 6;
        if (strpos($roleName, 'admin laa') !== false || strpos($roleName, 'laa') !== false) return $isUserRoleTable ? 5 : 1;
        if (strpos($roleName, 'ketua kk') !== false || strpos($roleName, 'kk') !== false) return $isUserRoleTable ? 9 : 7;
        if (strpos($roleName, 'admin') !== false) return 1;

        $roleField = $this->db->field_exists('role', $this->tbl_role) ? 'role' : 'name';
        $role = $this->db->get_where($this->tbl_role, [$roleField => $roleName])->row();
        return $role ? (int)$role->id : ($isUserRoleTable ? 4 : 5);
    }

    /**
     * Bulk Insert or Update user records in high-performance transactions
     * @param array $accounts
     * @return array ['imported' => int, 'updated' => int]
     */
    public function upsert_users_bulk($accounts)
    {
        if (empty($accounts)) {
            return ['imported' => 0, 'updated' => 0];
        }

        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        // Cache role IDs
        $roleField = $this->db->field_exists('role', $this->tbl_role) ? 'role' : 'name';
        $roleQuery = $this->db->get($this->tbl_role)->result_array();
        $roleMap = [];
        foreach ($roleQuery as $r) {
            $roleMap[strtolower(trim($r[$roleField]))] = (int)$r['id'];
        }

        // Collect unique valid emails
        $emails = [];
        foreach ($accounts as $acc) {
            $email = isset($acc['email']) ? strtolower(trim($acc['email'])) : '';
            if (!empty($email) && preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $email)) {
                $emails[] = $email;
            }
        }
        $emails = array_unique($emails);

        if (empty($emails)) {
            return ['imported' => 0, 'updated' => 0];
        }

        // Single query to find existing users
        $existingMap = [];
        $emailChunks = array_chunk($emails, 200);
        foreach ($emailChunks as $chunk) {
            $this->db->where_in('email', $chunk);
            $found = $this->db->get($this->tbl_user)->result_array();
            foreach ($found as $u) {
                $existingMap[strtolower(trim($u['email']))] = $u;
            }
        }

        $now = date('Y-m-d H:i:s');
        $toInsert = [];
        $importedCount = 0;
        $updatedCount = 0;

        $this->db->trans_start();

        $defaultRoleId = ($this->tbl_role === 'user_role') ? 4 : 5; // Mahasiswa

        foreach ($accounts as $acc) {
            $email = isset($acc['email']) ? strtolower(trim($acc['email'])) : '';
            if (empty($email) || !preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $email)) {
                continue;
            }

            $roleName = isset($acc['role']) ? strtolower(trim($acc['role'])) : 'mahasiswa';
            $roleId = isset($roleMap[$roleName]) ? $roleMap[$roleName] : $this->get_role_id_by_name($roleName);

            $token = isset($acc['token']) && !empty($acc['token']) ? trim($acc['token']) : null;
            $name = isset($acc['name']) && !empty($acc['name']) ? trim($acc['name']) : 'User';
            $nimNip = isset($acc['nim_nip']) ? trim($acc['nim_nip']) : '';
            $emailStatus = isset($acc['email_status']) ? $acc['email_status'] : 'belum';

            if (isset($existingMap[$email])) {
                // Update existing user
                $existing = $existingMap[$email];
                $updateData = [
                    'name' => $name,
                    'role_id' => $roleId
                ];
                if ($this->db->field_exists('nidn_nim', $this->tbl_user)) $updateData['nidn_nim'] = $nimNip;
                if ($this->db->field_exists('nim', $this->tbl_user)) $updateData['nim'] = $nimNip;
                if ($this->db->field_exists('updated_at', $this->tbl_user)) $updateData['updated_at'] = $now;

                if ($token && empty($existing['password_changed'])) {
                    if ($this->db->field_exists('token', $this->tbl_user)) $updateData['token'] = $token;
                    $updateData['password'] = password_hash($token, PASSWORD_DEFAULT, ['cost' => 8]);
                }
                $this->db->where('id', $existing['id']);
                $this->db->update($this->tbl_user, $updateData);
                $updatedCount++;
            } else {
                // Queue for bulk insert
                $rawPwd = $token ? $token : 'Telkom#123';
                $salt = bin2hex(random_bytes(16));
                $insertRow = [
                    'id' => uniqid('usr_'),
                    'username' => !empty($nimNip) ? $nimNip : explode('@', $email)[0],
                    'role_id' => $roleId,
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($rawPwd, PASSWORD_DEFAULT, ['cost' => 8]),
                    'status' => 'active'
                ];
                if ($this->db->field_exists('salt', $this->tbl_user)) $insertRow['salt'] = $salt;
                if ($this->db->field_exists('nidn_nim', $this->tbl_user)) $insertRow['nidn_nim'] = $nimNip;
                if ($this->db->field_exists('nim', $this->tbl_user)) $insertRow['nim'] = $nimNip;
                if ($this->db->field_exists('token', $this->tbl_user)) $insertRow['token'] = $token;
                if ($this->db->field_exists('password_changed', $this->tbl_user)) $insertRow['password_changed'] = 0;
                if ($this->db->field_exists('email_status', $this->tbl_user)) $insertRow['email_status'] = $emailStatus;
                if ($this->db->field_exists('is_active', $this->tbl_user)) $insertRow['is_active'] = 0;
                if ($this->db->field_exists('date_created', $this->tbl_user)) $insertRow['date_created'] = time();
                if ($this->db->field_exists('created_at', $this->tbl_user)) $insertRow['created_at'] = $now;
                if ($this->db->field_exists('updated_at', $this->tbl_user)) $insertRow['updated_at'] = $now;

                $toInsert[] = $insertRow;
                $importedCount++;

                // If batch reaches 100, insert chunk
                if (count($toInsert) >= 100) {
                    $this->db->insert_batch($this->tbl_user, $toInsert);
                    $toInsert = [];
                }
            }

            // Sync with user_token table if exists
            if ($token && $this->db->table_exists('user_token')) {
                $this->db->replace('user_token', [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ]);
            }
        }

        if (!empty($toInsert)) {
            $this->db->insert_batch($this->tbl_user, $toInsert);
            $toInsert = [];
        }

        $this->db->trans_complete();

        return [
            'imported' => $importedCount,
            'updated' => $updatedCount
        ];
    }

    /**
     * Insert or update user record by email
     * @param array $data
     * @return int User ID
     */
    public function upsert_user($data)
    {
        $email = strtolower(trim($data['email']));
        $existing = $this->db->get_where($this->tbl_user, ['email' => $email])->row();

        if ($existing) {
            $updateData = [
                'name' => $data['name'],
                'role_id' => isset($data['role_id']) ? $data['role_id'] : $existing->role_id
            ];
            if ($this->db->field_exists('nidn_nim', $this->tbl_user)) $updateData['nidn_nim'] = isset($data['nidn_nim']) ? $data['nidn_nim'] : ($existing->nidn_nim ?? '');
            if ($this->db->field_exists('nim', $this->tbl_user)) $updateData['nim'] = isset($data['nidn_nim']) ? $data['nidn_nim'] : ($existing->nim ?? '');
            if ($this->db->field_exists('updated_at', $this->tbl_user)) $updateData['updated_at'] = date('Y-m-d H:i:s');

            if (!empty($data['token']) && empty($existing->password_changed)) {
                $salt = bin2hex(random_bytes(16));
                if ($this->db->field_exists('salt', $this->tbl_user)) $updateData['salt'] = $salt;
                if ($this->db->table_exists('user_token')) {
                    $tokenHash = password_hash($data['token'], PASSWORD_DEFAULT, ['cost' => 8]);
                    $this->db->replace('user_token', [
                        'email' => $email,
                        'token' => $tokenHash,
                        'date_created' => time()
                    ]);
                } else if ($this->db->field_exists('token', $this->tbl_user)) {
                    $updateData['token'] = $data['token'];
                    $updateData['password'] = password_hash($data['token'], PASSWORD_DEFAULT, ['cost' => 8]);
                }
            }
            $this->db->where('id', $existing->id);
            $this->db->update($this->tbl_user, $updateData);
            return $existing->id;
        } else {
            $rawToken = !empty($data['token']) ? $data['token'] : null;
            $salt = bin2hex(random_bytes(16));
            $insertData = [
                'id' => uniqid('usr_'),
                'username' => !empty($data['nidn_nim']) ? $data['nidn_nim'] : explode('@', $email)[0],
                'role_id' => isset($data['role_id']) ? $data['role_id'] : (($this->tbl_role === 'user_role') ? 4 : 5),
                'name' => $data['name'],
                'email' => $email,
                'password' => password_hash('Telkom#123', PASSWORD_DEFAULT, ['cost' => 8]),
                'salt' => $salt,
                'status' => 'active'
            ];
            if ($this->db->field_exists('salt', $this->tbl_user)) $insertData['salt'] = $salt;
            if ($this->db->field_exists('nidn_nim', $this->tbl_user)) $insertData['nidn_nim'] = isset($data['nidn_nim']) ? $data['nidn_nim'] : '';
            if ($this->db->field_exists('nim', $this->tbl_user)) $insertData['nim'] = isset($data['nidn_nim']) ? $data['nidn_nim'] : '';
            if ($this->db->field_exists('password_changed', $this->tbl_user)) $insertData['password_changed'] = 0;
            if ($this->db->field_exists('email_status', $this->tbl_user)) $insertData['email_status'] = isset($data['email_status']) ? $data['email_status'] : 'belum';
            if ($this->db->field_exists('is_active', $this->tbl_user)) $insertData['is_active'] = 0;
            if ($this->db->field_exists('date_created', $this->tbl_user)) $insertData['date_created'] = time();
            if ($this->db->field_exists('created_at', $this->tbl_user)) $insertData['created_at'] = date('Y-m-d H:i:s');
            if ($this->db->field_exists('updated_at', $this->tbl_user)) $insertData['updated_at'] = date('Y-m-d H:i:s');

            if ($this->tbl_user === 'users' && $this->db->field_exists('token', 'users')) {
                $insertData['token'] = $rawToken;
                if ($rawToken) {
                    $insertData['password'] = password_hash($rawToken, PASSWORD_DEFAULT, ['cost' => 8]);
                }
            }

            $this->db->insert($this->tbl_user, $insertData);
            $newId = $insertData['id'];

            if ($rawToken && $this->db->table_exists('user_token')) {
                $tokenHash = password_hash($rawToken, PASSWORD_DEFAULT, ['cost' => 8]);
                $this->db->replace('user_token', [
                    'email' => $email,
                    'token' => $tokenHash,
                    'date_created' => time()
                ]);
            }

            return $newId;
        }
    }

    /**
     * Bulk update tokens for multiple users strictly in user_token table
     * @param array $updates [['id' => 'usr_...', 'token' => '...'], ...]
     * @return int
     */
    public function update_user_tokens_bulk($updates)
    {
        if (empty($updates)) return 0;
        @set_time_limit(300);
        $this->db->trans_start();
        $count = 0;
        $now = time();
        foreach ($updates as $item) {
            $id = (string)$item['id'];
            $token = $item['token'];

            $user = $this->get_by_id($id);
            if ($user && !empty($user->email)) {
                // 1. Save strictly to user_token table
                if ($this->db->table_exists('user_token')) {
                    $this->db->replace('user_token', [
                        'email' => $user->email,
                        'token' => $token,
                        'date_created' => $now
                    ]);
                }

                // 2. Keep token column in sync if exists, but DO NOT touch user.password!
                $userUpdates = [];
                if ($this->db->field_exists('token', $this->tbl_user)) {
                    $userUpdates['token'] = $token;
                }
                if ($this->db->field_exists('updated_at', $this->tbl_user)) {
                    $userUpdates['updated_at'] = date('Y-m-d H:i:s');
                }
                if (!empty($userUpdates)) {
                    $this->db->where('id', (string)$id)->update($this->tbl_user, $userUpdates);
                }
                $count++;
            }
        }
        $this->db->trans_complete();
        return $count;
    }

    /**
     * Update user token strictly in user_token table
     * @param mixed $id
     * @param string $token
     * @return bool
     */
    public function update_user_token($id, $token)
    {
        $user = $this->get_by_id($id);
        if (!$user || empty($user->email)) {
            return false;
        }

        if ($this->db->table_exists('user_token')) {
            $this->db->replace('user_token', [
                'email' => $user->email,
                'token' => $token,
                'date_created' => time()
            ]);
        }

        if ($this->db->field_exists('token', $this->tbl_user)) {
            $this->db->where('id', (string)$id)->update($this->tbl_user, [
                'token' => $token,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return true;
    }

    /**
     * Update email status and sent_at timestamp
     * @param mixed $id
     * @param string $status ('terkirim', 'gagal', 'belum')
     * @return bool
     */
    public function update_email_status($id, $status)
    {
        $user = $this->get_by_id($id);
        if (!$user) return false;

        $now = date('Y-m-d H:i:s');

        // Log into log_approval_history table
        if ($this->db->table_exists('log_approval_history')) {
            $actorName = $this->session->userdata('name') ?? 'Administrator';
            $actorRole = $this->session->userdata('role') ?? 'Admin';
            $actorNipNim = $this->session->userdata('nim') ?? ($this->session->userdata('nip') ?? '-');

            $this->db->insert('log_approval_history', [
                'modul' => 'Import Email',
                'ref_id' => (string)$user->email,
                'target_name' => $user->name,
                'action' => ($status === 'terkirim') ? 'Email Sent' : ($status === 'gagal' ? 'Email Failed' : 'Email Pending'),
                'actor_id' => (int)$this->session->userdata('id'),
                'actor_name' => $actorName,
                'actor_role' => $actorRole,
                'actor_nip_nim' => $actorNipNim,
                'catatan' => json_encode(['status' => $status, 'email' => $user->email, 'sent_at' => $now]),
                'created_at' => $now
            ]);
        }

        $data = [];
        if ($this->db->field_exists('email_status', $this->tbl_user)) $data['email_status'] = $status;
        if ($this->db->field_exists('updated_at', $this->tbl_user)) $data['updated_at'] = $now;
        if ($status === 'terkirim' && $this->db->field_exists('email_sent_at', $this->tbl_user)) {
            $data['email_sent_at'] = $now;
        }

        if (!empty($data)) {
            $this->db->where('id', (string)$id);
            return $this->db->update($this->tbl_user, $data);
        }

        return true;
    }

    /**
     * Bulk delete users by array of IDs
     * @param array $ids
     * @return bool
     */
    public function delete_users_batch($ids)
    {
        if (empty($ids)) return false;
        $this->db->where_in('id', $ids);
        return $this->db->delete($this->tbl_user);
    }

    /**
     * Reset imported users (keeps default master accounts)
     * @return bool
     */
    public function reset_imported_users()
    {
        $this->db->where_not_in('id', ['admin-01', 'mhs-1301210001', 'dsn-wali-01', 'koor-ta-01', 'admin-laa-01']);
        $res = $this->db->delete($this->tbl_user);

        if ($this->db->table_exists('log_approval_history')) {
            $this->db->where('modul', 'Import Email')->delete('log_approval_history');
        }
        return $res;
    }
}
