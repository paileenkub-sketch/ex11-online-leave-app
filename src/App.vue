<script setup>
import { ref } from 'vue'
import { API_URL } from './config'

const page = ref('home')
const user = ref(null)
const leaves = ref([])
const msg = ref('')
const loginForm = ref({ username: '', password: '' })
const leaveForm = ref({ leave_type: 'ลาป่วย', date_from: '', date_to: '', reason: '' })

const loadLeaves = async () => {
  const r = await fetch(`${API_URL}/leave_api.php`)
  leaves.value = (await r.json()).data || []
}
const doLogin = async () => {
  const r = await fetch(`${API_URL}/leave_api.php`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'login', ...loginForm.value }) })
  const j = await r.json()
  if (j.success) { user.value = j.user; page.value = 'app'; await loadLeaves() }
  else msg.value = j.message
}
const submitLeave = async () => {
  const r = await fetch(`${API_URL}/leave_api.php`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'create', user_id: user.value.user_id, ...leaveForm.value }) })
  msg.value = (await r.json()).message
  leaveForm.value = { leave_type: 'ลาป่วย', date_from: '', date_to: '', reason: '' }
  await loadLeaves()
}
const setStatus = async (id, st) => {
  const r = await fetch(`${API_URL}/leave_api.php`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'status', leave_id: id, status: st }) })
  msg.value = (await r.json()).message; await loadLeaves()
}
</script>

<template>
  <nav class="navbar navbar-dark bg-success">
    <div class="container">
      <span class="navbar-brand fw-semibold">ระบบยื่นใบลางานออนไลน์</span>
      <div v-if="user" class="d-flex align-items-center gap-2 text-white small">
        <span>{{ user.fullname }} ({{ user.role }})</span>
        <button class="btn btn-outline-light btn-sm" @click="user = null; page = 'home'">ออกจากระบบ</button>
      </div>
      <button v-else class="btn btn-light btn-sm" @click="page = 'login'">เข้าสู่ระบบ</button>
    </div>
  </nav>
  <div class="container my-4">
    <div v-if="msg" class="alert alert-info py-2">{{ msg }}</div>
    <div v-if="page === 'home'" class="card">
      <div class="card-body">
        <h5>ยินดีต้อนรับสู่ระบบการลางานออนไลน์</h5>
        <p class="text-muted">กรุณาเข้าสู่ระบบเพื่อยื่นคำขอลาป่วย ลากิจ ลาพักร้อน และติดตามผลการอนุมัติ</p>
        <button class="btn btn-success btn-sm" @click="page = 'login'">ไปยังหน้า Login</button>
      </div>
    </div>
    <div v-else-if="page === 'login'" class="card mx-auto" style="max-width: 400px">
      <div class="card-header fw-semibold">เข้าสู่ระบบลางาน</div>
      <div class="card-body">
        <form @submit.prevent="doLogin">
          <div class="mb-2"><label class="form-label">Username</label><input v-model="loginForm.username" class="form-control" required /></div>
          <div class="mb-3"><label class="form-label">Password</label><input v-model="loginForm.password" type="password" class="form-control" required /></div>
          <button class="btn btn-success w-100">Login</button>
        </form>
        <div class="small text-muted mt-2">พนักงาน: <code>staff01</code> | หัวหน้างาน: <code>boss01</code> (รหัส <code>123456</code>)</div>
      </div>
    </div>
    <div v-else>
      <form v-if="user.role === 'staff'" class="row g-2 mb-4" @submit.prevent="submitLeave">
        <div class="col-md-2"><select v-model="leaveForm.leave_type" class="form-select form-select-sm"><option>ลาป่วย</option><option>ลากิจ</option><option>ลาพักร้อน</option></select></div>
        <div class="col-md-2"><input v-model="leaveForm.date_from" type="date" class="form-control form-control-sm" required /></div>
        <div class="col-md-2"><input v-model="leaveForm.date_to" type="date" class="form-control form-control-sm" required /></div>
        <div class="col-md-4"><input v-model="leaveForm.reason" class="form-control form-control-sm" placeholder="เหตุผลการลา" required /></div>
        <div class="col-md-2"><button class="btn btn-success btn-sm w-100">ส่งใบลา</button></div>
      </form>
      <table class="table table-bordered align-middle">
        <thead class="table-success"><tr><th>#</th><th>พนักงาน</th><th>ประเภท</th><th>วันที่</th><th>เหตุผล</th><th>สถานะ</th><th v-if="user.role === 'supervisor'">อนุมัติ</th></tr></thead>
        <tbody>
          <tr v-for="l in leaves" :key="l.leave_id">
            <td>{{ l.leave_id }}</td><td>{{ l.fullname }}</td><td>{{ l.leave_type }}</td>
            <td>{{ l.date_from }} - {{ l.date_to }}</td><td>{{ l.reason }}</td>
            <td><span class="badge bg-secondary">{{ l.status }}</span></td>
            <td v-if="user.role === 'supervisor'">
              <button class="btn btn-success btn-sm me-1" @click="setStatus(l.leave_id, 'อนุมัติ')">อนุมัติ</button>
              <button class="btn btn-danger btn-sm" @click="setStatus(l.leave_id, 'ไม่อนุมัติ')">ไม่อนุมัติ</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
