let baseUrl = ''
if (process.env.NODE_ENV === 'production') {
   baseUrl = ''
else {
   baseUrl = '/bank'
}

export const apiHost = baseUrl