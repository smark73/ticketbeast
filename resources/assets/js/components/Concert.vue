<template>
  <div>
    <p>{{ concertTitle }}</p>
    <p>{{ concertSubtitle }}</p>
    <p>{{ date }}</p>
    <p>{{ startTime }}</p>
    <p>{{ ticketPrice }}</p>
    <p>{{ venue }}</p>
    <p>{{ venueAddress }}</p>
    <p>{{ venueCity }}</p>
    <p>{{ venueState }}</p>
    <p>{{ venueZip }}</p>
    <p>{{ additionalInfo }}</p>

    <form>
      <input
          v-model="typeAway1"
          type="text"
          placeholder="type something"
          class="typeAwayInput"
          onchange="this.combineBoth"
      />
      <input
          v-model="typeAway2"
          type="text"
          placeholder="type something"
          class="typeAwayInput"
          onchange="this.combineBoth"
      />

    </form>

    <p>typeAway1: {{ typeAway1 }}</p>
    <p>typeAway2: {{ typeAway2 }}</p>
    <p>typeAway3: {{ this.typeAway3 }}</p>
    <p>combineBoth: {{ this.combineBoth }}</p>
    <p>typeCount: {{ this.typeCount }}</p>
    <p>watching typeCount: {{ this.watchCount }}</p>
  </div>
</template>

<script>
export default {
  name: "Concert",
  props: {
    concertTitle: '',
    concertSubtitle: '',
    date: '',
    startTime: '',
    ticketPrice: '',
    venue: '',
    venueAddress: '',
    venueCity: '',
    venueState: '',
    venueZip: '',
    additionalInfo: '',
  },
  data() {
    return {
      typeAway1: 'default',
      typeAway2: '',
      typeAway3: this.combineBoth,
      typeCount: 0,
      watchCount: '',
      // concertTitle: '',
      // concertSubtitle: '',
      // date: '',
      // startTime: '',
      // ticketPrice: '',
      // venue: '',
      // venueAddress: '',
      // venueCity: '',
      // venueState: '',
      // venueZip: '',
      // additionalInfo: '',
    }
  },
  computed: {
    combineBoth() {
      this.typeCount += 1;
      return this.typeAway1 + " " + this.typeAway2;

    }
  },
  watch: {
    typeCount(val, newVal) {
      this.watchCount = val + " " + newVal;
    }
  }
}
</script>

<style scoped>
.typeAwayInput {
  font-size: 16px;
  height: 40px;
  width: 400px;
  border: 1px solid black;
}
</style>