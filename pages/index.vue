<template>
  <div class="l-content">
    <dl>
      <dt>Q.</dt>
      <dd>{{ now.question }}</dd>
    </dl>
    <p></p>
    <div v-show="flg === 0">
      <textarea v-model="writeAnswer" />
      <button @click="flg = 1">答え合わせ</button>
    </div>
    <div v-show="flg === 1">
      <dl>
        <dt>A.</dt>
        <dd>{{ now.answer }}</dd>
      </dl>
      <dl>
        <dt>U.</dt>
        <dd>{{ writeAnswer }}</dd>
      </dl>
      <button @click="mistakeQuestion">✗</button>
      <button @click="correctQuestion">○</button>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from 'vue'
import { ThisTypedComponentOptionsWithRecordProps } from 'vue/types/options'

type Question = {
  id: string
  question: string
  answer: string
  reference: string
  appearCount: string
  correctCount: string
}

type DataType = {
  spreadSheetData: [],
  counter: number,
  flg: number,
  writeAnswer:  string
}

type MethodType = {
  getQuestions: any,
  nextQuestion: any
}

type ComputedType = {
  items: [Question],
  now: Question
}

type PropType = {}

export default Vue.extend({
  name: 'IndexPage',
  data() {
    return {
      counter: 0, // 何問目か
      flg: 0,  //0: 問題表示, 1: 解答表示
      writeAnswer: '',
      spreadSheetData: []
    }
  },
  created() {
    this.getQuestions()
  },
  methods: {
    async mistakeQuestion() {
      const params = new URLSearchParams()
      const appearCount = this.now.appearCount ? this.now.appearCount : '0'
      const correctCount = this.now.correctCount ? this.now.correctCount : '0'
      params.append('id', String(Number(this.now.id) + 2))
      params.append('total', String(Number(appearCount) + 1))
      params.append('correct', correctCount)
      const res = await this.$axios.post('/add', params)
      this.nextQuestion()
    },
    async correctQuestion() {
      const params = new URLSearchParams()
      const appearCount = this.now.appearCount ? this.now.appearCount : '0'
      const correctCount = this.now.correctCount ? this.now.correctCount : '0'
      params.append('id', String(Number(this.now.id) + 2))
      params.append('total', String(Number(appearCount) + 1))
      params.append('correct', String(Number(correctCount) + 1))
      const res = await this.$axios.post('/add', params)
      this.nextQuestion()
    },
    nextQuestion() {
      this.writeAnswer = ''
      this.flg = 0

      // 最後の問題まで表示したら頭に戻る
      this.counter = this.counter !== this.spreadSheetData.length - 1 ?
        this.counter + 1 : 0
    },
    async getQuestions() {
      this.spreadSheetData = await this.$axios.$get('/api')
    }
  },
  computed: {
    items() {
      return this.spreadSheetData.sort((prev: Question, next: Question) => {
        // 値が無かったら0とする
        // 正答率を求める
        const prevRate = Number(prev.appearCount) ? Number(prev.correctCount) / Number(prev.appearCount) : 0
        const nextRate = Number(next.appearCount) ? Number(next.correctCount) / Number(next.appearCount) : 0
        return (prevRate < nextRate) ? -1 : 1
      })
    },
    now() {
      return (this.items.length) ? this.items[this.counter] : ''
    }
  }
} as ThisTypedComponentOptionsWithRecordProps<Vue, DataType, MethodType, ComputedType, PropType>)
</script>

<style>
.l-content {
  margin: 0 auto;
  max-width: 640px;
  padding: 0 20px;
}

textarea {
  display: block;
  width: 100%;
}
</style>
